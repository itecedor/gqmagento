<?php

namespace POSIMWebExt\GCLink\Controller\Pay;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Remove extends \Magento\Framework\App\Action\Action
{
    protected $posimgiftcard;
    protected $cart;
    protected $logger;
    protected $quoteRepository;

    public function __construct(Context $context,
                                \Magento\Checkout\Model\Cart $cart,
                                \POSIMWebExt\GCLink\Logger\Logger $logger,
                                \POSIMWebExt\GCLink\Model\Total\Posimgiftcard $posimgiftcard,
                                \Magento\Quote\Model\QuoteRepository $quoteRepository
    )
    {
        $this->cart = $cart;
        $this->quoteRepository = $quoteRepository;
        $this->posimgiftcard = $posimgiftcard;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $quote = $this->cart->getQuote();
        $totals = $this->cart->getQuote()->getTotals();
        foreach ($totals as $total) {
            $this->posimgiftcard->clearValues($total, $quote);
        }
        $quote->setPosimgiftcard(0);
        $quote->setBasePosimgiftcard(0);
        $quote->setGrandTotal(0);
        $quote->setBaseGrandTotal(0);
        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();
        $this->quoteRepository->save($quote);
        if ($quote->getPosimgiftcard() == 0) {
            $result['code'] = 'ok';
            $result['message'] = 'Gift card removed successfully';
        } else {
            $result['code'] = 'error';
            $result['message'] = 'Gift card was not removed successfully.';
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}