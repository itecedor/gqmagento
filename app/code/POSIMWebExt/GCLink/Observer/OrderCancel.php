<?php

namespace POSIMWebExt\GCLink\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class OrderCancel implements ObserverInterface
{
    protected $logger;
    protected $helper;

    public function __construct(\POSIMWebExt\GCLink\Logger\Logger $logger,
                                \POSIMWebExt\GCLink\Helper\Data $helper
    )
    {
        $this->logger = $logger;
        $this->helper = $helper;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $incrementId = $order->getIncrementId();
        $giftCard = $order->getPosimgiftcard();
        $gcNum = $order->getData('posimgc_num');
        $options = array(
            'x_Card_Num'    => $gcNum,
            'x_Type'        => 'CR',
            'x_Amount'      => 0.00,
            'x_Invoice_Num' => $order->getIncrementId()
        );
        /*
         * Always do a credit of 0, because this just voids the transaction
         * with the same invoice id on the GCLink database.
         */
        $gcLinkResponse = $this->helper->postGCLinkTransaction($options);
        //$this->logger->addDebug($gcLinkResponse);
    }
}