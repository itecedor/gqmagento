<?php

namespace POSIMWebExt\GCLink\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $ch;
    protected $scopeConfig;
    protected $resourceConfig;
    protected $gcUser;
    protected $gcPass;
    protected $gcUrl;
    protected $gcStoreId;
    protected $emailInlineTrans;
    protected $emailTransportBuilder;
    protected $priceHelper;
    protected $logger;
    protected $cardsFactory;
    protected $dateTime;

    public function __construct(Context $context,
                                \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig,
                                \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
                                \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
                                \Magento\Framework\Pricing\Helper\Data $priceHelper,
                                \POSIMWebExt\GCLink\Logger\Logger $logger,
                                \POSIMWebExt\GCLink\Model\CardsFactory $cardsFactory,
                                \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    )
    {
        $this->ch = curl_init();
        $this->scopeConfig = $context->getScopeConfig();
        $this->resourceConfig = $resourceConfig;
        $this->gcUrl = $this->scopeConfig->getValue('gclink/connection/gclink_url');
        $this->gcUser = $this->scopeConfig->getValue('gclink/connection/gclink_user');
        $this->gcPass = $this->scopeConfig->getValue('gclink/connection/gclink_pass');
        $this->gcStoreId = $this->scopeConfig->getValue('gclink/connection/gclink_store_id');
        $this->emailInlineTrans = $inlineTranslation;
        $this->emailTransportBuilder = $transportBuilder;
        $this->priceHelper = $priceHelper;
        $this->cardsFactory = $cardsFactory;
        $this->logger = $logger;
        $this->dateTime = $dateTime;
        parent::__construct($context);
    }

    protected function formatCredentials()
    {
        $options = array(
            'url'    => $this->gcUrl,
            'fields' => array(
                'x_Login'    => urlencode($this->gcUser),
                'x_Password' => urlencode($this->gcPass),
                'x_Store_ID' => urlencode($this->gcStoreId)
            )
        );

        return $options;
    }

    public function getCardBalance($cardNum)
    {
        $gcData = $this->loadGCLinkData(array('type' => 'BR', 'cardnum' => $cardNum));

        return $gcData[11];
    }

    public function loadGCLinkData($options)
    {
        if (empty($options['type'])) {
            exit('please provide gclink type');
        } else {
            $gcData = $this->formatCredentials();
            $gcData['fields']['x_Card_Num'] = urlencode($options['cardnum']);
            $gcData['fields']['x_Type'] = urlencode($options['type']);
            $result = explode('|', $this->getGCLinkData($gcData));

            return $result;
        }
    }

    public function getGCLinkData($options)
    {
        $ch = $this->ch;
        $fields_string = '';
        //url-ify the data for the POST
        foreach ($options['fields'] as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $options['url']);
        curl_setopt($ch, CURLOPT_POST, count($options['fields']));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //execute post
        return curl_exec($ch);
    }

    public function getMinAllowedValue()
    {
        return $this->scopeConfig->getValue('gclink/defaults/min_value');
    }

    /**
     * @param $options
     *
     * @return mixed
     */
    public function postGCLinkTransaction($options)
    {
        $gcData = $this->formatCredentials();
        $gcData['fields']['x_Card_Num'] = $options['x_Card_Num'];
        $gcData['fields']['x_Type'] = $options['x_Type'];
        $gcData['fields']['x_Amount'] = $options['x_Amount'];
        $gcData['fields']['x_Invoice_Num'] = $options['x_Invoice_Num'];
        $response = explode('|', $this->getGCLinkData($gcData));
        if ($response[0] == 1) {
            $this->addGiftCardToMagento($response);
        }

        return $response;
    }

    public function addGiftCardToMagento($gclinkResponse)
    {
        /* if card already exists, just update the balance and invoice id */
        $card = $this->cardsFactory->create();
        if ($card->load($gclinkResponse[5], 'gc_num')) {
            $card->setInvoiceId($gclinkResponse[6]);
            if ($card->getGiftCardNum() == NULL) {
                $card->setGiftCardNum($gclinkResponse[5]);

            }
        } else {
            $card->setGiftCardNum($gclinkResponse[5]);
        }
        if ($gclinkResponse[9] == 'SL') {
            $card->setGiftCardPurchase($gclinkResponse[8]);
        } elseif ($gclinkResponse[9] == 'PY') {
            $card->setGiftCardPayment($gclinkResponse[8]);
        }
        $card->setInvoiceId($gclinkResponse[6]);
        $card->setBalance($gclinkResponse[11]);
        $card->setCreationTime($this->dateTime->date());
        $card->save();
    }

    public function getNextCardNum()
    {
        $nextCardNum = $this->scopeConfig->getValue('gclink/defaults/next_num');
        $increment = $this->scopeConfig->getValue('gclink/defaults/num_increment') != 0 ? $this->scopeConfig->getValue('gclink/defaults/num_increment') : 1;
        $increment += $nextCardNum;
        $this->resourceConfig->saveConfig('gclink/defaults/next_num', $increment, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        return $this->scopeConfig->getValue('gclink/defaults/prefix') . $nextCardNum;
    }

    public function sendActivatedEmail($amount, $gcNum, $gcRecipEmail, $gcRecipName, $gcGiftMessage, $customerEmail)
    {
        $templateField = 'gclink/email_options/activated_email';
        $templateId = $this->scopeConfig->getValue($templateField);
        $formattedAmount = $this->priceHelper->currency($amount, true, false);
        $this->emailInlineTrans->suspend();
        $this->emailTransportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                array(
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
                )
            )
            ->setTemplateVars(array(
                'gc_amount'    => $formattedAmount,
                'gc_number'    => $gcNum,
                'gifter'       => $customerEmail,
                'recipient'    => $gcRecipName,
                'gift_message' => $gcGiftMessage))
            ->setFrom(array('name' => $this->scopeConfig->getValue('general/store_information/name'), 'email' => $this->scopeConfig->getValue('trans_email/ident_sales/email')))
            ->addTo($gcRecipEmail, $gcRecipName);
        $transport = $this->emailTransportBuilder->getTransport();
        $transport->sendMessage();
        $this->emailInlineTrans->resume();
    }

    public function calculateGiftCard($quote)
    {
        $giftCardAmountToApply = 0;
        $posimGiftCard = -($quote->getData('posimgiftcard'));
        if ($posimGiftCard) {
            if ($posimGiftCard > 0) {
                $giftCardBalance = $posimGiftCard;
                if ($giftCardBalance < $quote->getGrandTotal()) {
                    $giftCardAmountToApply = $giftCardBalance;
                } elseif ($giftCardBalance == $quote->getGrandTotal()) {
                    $giftCardAmountToApply = $giftCardBalance;
                } elseif ($giftCardBalance > $quote->getGrandTotal()) {
                    $giftCardAmountToApply = $quote->getGrandTotal();
                }
            }
        }

        return $giftCardAmountToApply;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return array|bool|float|int|null|string
     */
    public function getGiftcardNumber(\Magento\Quote\Model\Quote $quote)
    {
        return $quote->getData('posimgc_num');
    }

    public function setGiftCardNumber($gcNum, \Magento\Quote\Model\Quote $quote)
    {
        $quote->setData('posimgc_num', $gcNum);
    }
}
