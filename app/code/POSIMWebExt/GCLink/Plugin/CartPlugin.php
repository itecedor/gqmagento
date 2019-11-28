<?php

namespace POSIMWebExt\GCLink\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Zend\Validator\EmailAddress;

class CartPlugin
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;
    protected $request;
    protected $scopeConfig;
    protected $errorMessage;
    protected $productId;
    protected $catalogSession;
    protected $cart;
    protected $serializer;
    protected $logger;

    /**
     * Plugin constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \POSIMWebExt\GCLink\Logger\Logger $logger
    )
    {
        $this->quote = $checkoutSession->getQuote();
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->errorMessage = '';
        $this->cart = $cart;
        $this->catalogSession = $catalogSession;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * beforeAddProduct
     *
     * @param                                                 $subject
     * @param      \Magento\Catalog\Model\Product\Interceptor $productInfo
     * @param null                                            $requestInfo
     *
     * @return array
     * @throws LocalizedException
     */
    public function beforeAddProduct($subject, $productInfo, $requestInfo = NULL)
    {
        if ($productInfo->getTypeId() == 'posimgc'):
            $giftCardConfig = array(
                'amount'         => (float)$this->request->getPost('giftcard_amount'),
                'type'           => $this->request->getPost('gctype'),
                'email'          => $this->request->getPost('gc_recipient_email'),
                'recip_name'     => $this->request->getPost('gc_recipient_name'),
                'gc_giftmessage' => $this->request->getPost('gc_gift_message')
            );
            $productInfo->setOptions(array('gctype' => $giftCardConfig['type']));
            $productInfo->setData('gctype', $this->request->getPost('gctype'));
            if (!$this->checkAgainstGiftCardSettings($giftCardConfig) || !$this->allowedQtyInQuote()):
                throw new LocalizedException(__($this->errorMessage));
            else:
                $priceToSet = (float)$giftCardConfig['amount'];
                $this->catalogSession->setData('posimgc_settings', $giftCardConfig);
                $productInfo->setPrice($priceToSet);
                $productInfo->setOriginalCustomPrice($priceToSet);
                $productInfo->setFinalPrice($priceToSet);
                $productInfo->setIsSuperMode(true);
                $productInfo->setIsVirtual(1);
            endif;
        endif;

        return [$productInfo, $requestInfo];
    }

    /**
     * @param $giftCardRequest
     *
     * @return bool
     */
    public function checkAgainstGiftCardSettings($giftCardRequest)
    {
        if ($giftCardRequest == NULL) {
            $this->errorMessage = "Unable to add gift card, please try again.";

            return false;
        } else {
            if (!$this->isAmountAllowed($giftCardRequest['amount'])) {
                return false;
            }
            //check to make sure either Physical or Virtual delivery have been chosen
            if ($giftCardRequest['type'] !== 'virtual' && $giftCardRequest['type'] !== 'physical') {
                $this->errorMessage = "Please select a gift card type and try again.";

                return false;
            } else {
                //validate selected card type is allowed
                if ($this->isTypeAllowed($giftCardRequest['type'])) {
                    if ($giftCardRequest['type'] == 'virtual') {
                        $validator = new EmailAddress();
                        if ($validator->isValid($giftCardRequest['email'])) {
                            // email appears to be valid
                        } else {
                            $this->errorMessage = "Email address appears to be invalid. Please check and try again.";

                            return false;
                        }
                    }
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param $type
     *
     * @return bool
     */
    public function isTypeAllowed($type)
    {
        $configCheck = 'gclink/defaults/enable_' . $type;
        if (!$this->scopeConfig->getValue($configCheck)) {
            $this->errorMessage = "Gift Card of " . $type . " type is not allowed by system configuration. Please try again.";

            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $amount
     *
     * @return bool
     */
    public function isAmountAllowed($amount)
    {
        $amount = (float)$amount;
        $minValue = (float)$this->scopeConfig->getValue('gclink/defaults/min_value');
        $maxValue = (float)$this->scopeConfig->getValue('gclink/defaults/max_value');
        if ($amount > $maxValue) {
            $this->errorMessage = "Requested amount exceeds Maximum Allowed Value. Please try again.";

            return false;
        } elseif ($amount < $minValue) {
            $this->errorMessage = "Requested amount is less than Minimum Required Value. Please try again.";

            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function allowedQtyInQuote()
    {
        $items = $this->quote->getAllItems();
        foreach ($items as $item) {
            if ($item->getProductType() == 'posimgc') {
                $this->errorMessage = "No more than one gift card product is allowed in cart. ";
                $this->errorMessage .= "Please place multiple orders to purchase multiple gift cards.";

                return false;
            }
        }

        return true;
    }

    public function afterAddProduct($subject, $productInfo, $requestInfo = NULL)
    {
        $productInfo = $this->cart->getQuote()->getItemsCollection();
        foreach ($productInfo as $item) {
            if ($item->getProductType() == 'posimgc') {
                $this->setGCAdditionalOptions($item);
            }
        }
    }

    public function setGCAdditionalOptions($quoteItem)
    {
        $giftCardConfig = $this->catalogSession->getData('posimgc_settings');
        $additionalOptions = array();
        $setPrice = 0;
        if ($additionalOption = $quoteItem->getOptionByCode('additional_options')) {
            $additionalOptions = (array)$this->serializer->unserialize($additionalOption->getValue());
        }
        if (is_array($giftCardConfig)) {
            foreach ($giftCardConfig as $key => $value) {
                if ($key == '' || $value == '') {
                    continue;
                }
                if ($key == 'amount') {
                    $setPrice = (float)$value;
                }
                $additionalOptions[] = [
                    'label' => $key,
                    'value' => $value
                ];
            }
        }
        if (count($additionalOptions) > 0) {
            $quoteItem->addOption(array(
                'code'  => 'additional_options',
                'value' => $this->serializer->serialize($additionalOptions)
            ));
            if ($setPrice > 0) {
                $quoteItem->setFinalPrice($setPrice);
                $quoteItem->setOriginalCustomPrice($setPrice);
                $quoteItem->setPrice($setPrice);
            }
        }
    }
}
