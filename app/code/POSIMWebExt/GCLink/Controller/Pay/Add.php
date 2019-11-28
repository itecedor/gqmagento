<?php

namespace POSIMWebExt\GCLink\Controller\Pay;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository;

class Add extends \Magento\Framework\App\Action\Action
{
    protected $cart;
    protected $cartHelper;
    protected $gclinkHelper;
    protected $quote;
    protected $logger;
    protected $quoteRepository;
    protected $serializer;

    public function __construct(Context $context,
                                \Magento\Checkout\Model\Cart $cart,
                                \Magento\Checkout\Helper\Cart $cartHelper,
                                \POSIMWebExt\GCLink\Helper\Data $gclinkHelper,
                                \POSIMWebExt\GCLink\Logger\Logger $logger,
                                \Magento\Quote\Model\QuoteRepository $quoteRepository
    )
    {
        parent::__construct($context);
        $this->cart = $cart;
        $this->quote = $cart->getQuote();
        $this->cartHelper = $cartHelper;
        $this->gclinkHelper = $gclinkHelper;
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
    }

    public function execute()
    {
        $result = array();
        $params = $this->getRequest()->getParams();
        try {
            $giftCard = $params['posimgc_num'];
            $cardBalance = (float)$this->gclinkHelper->getCardBalance($giftCard);
            if ($cardBalance > 0.00) {
                $this->quote->setData('posimgc_num', $giftCard);
                $this->quote->setPosimgiftcard(-$cardBalance);
                $amountToApply = $this->gclinkHelper->calculateGiftCard($this->quote);
                $this->quote->setPosimgiftcard(-$amountToApply);
                $this->quote->setTotalsCollectedFlag(false);
                $this->quote->collectTotals();
                $this->quoteRepository->save($this->quote);
                $result['code'] = 'ok';
                $result['message'] = __('Gift Card was applied successfully');
            } else {
                $result['code'] = 'error';
                $result['message'] = __('Error: Gift Card does not have a valid balance.');
            }
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($result);

            return $resultJson;
        } catch (\Exception $e) {
            $this->logger->addDebug('there was an exception in the add gift card controller.');
            $this->logger->addDebug('the message is ' . $e->getMessage());
            $this->logger->addDebug('the trace is ' . $e->getTraceAsString());
            $result['code'] = 'error';
            $result['message'] = __('Error: Gift Card was not applied successfully.');
        }
        /*
           * @var \Magento\Framework\Controller\Result\Json $resultJson
         */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}