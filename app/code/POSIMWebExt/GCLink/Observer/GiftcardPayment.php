<?php

namespace POSIMWebExt\GCLink\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class GiftcardPayment implements ObserverInterface
{
    protected $logger;
    protected $helper;
    protected $orderExtensionFactory;

    public function __construct(\POSIMWebExt\GCLink\Logger\Logger $logger,
                                \POSIMWebExt\GCLink\Helper\Data $helper,
                                \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
    )
    {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    public function execute(Observer $observer)
    {
        $quote = $observer->getQuote();
        $giftcard = $quote->getPosimgiftcard();
        $baseGiftcard = $quote->getBasePosimgiftcard();
        $giftcardNumber = $this->helper->getGiftcardNumber($quote);
        if (!$giftcard || !$baseGiftcard) {
            return $this;
        }

		if ($giftcardNumber) {
			$order = $observer->getOrder();
			$order->setData('posimgiftcard', -$giftcard);
			$order->setData('base_posimgiftcard', -$baseGiftcard);
			$order->setData('posimgc_num', $giftcardNumber);
			$orderIncrementId = $order->getIncrementId();
			//$this->logger->addDebug('Deducting Gift Card amount ' . -$giftcard . ' from GCLINK');
			$options = array(
				'x_Card_Num'    => $giftcardNumber,
				'x_Type'        => 'PY',
				'x_Amount'      => -$giftcard,
				'x_Invoice_Num' => $orderIncrementId
			);
			$gcLinkResponse = $this->helper->postGCLinkTransaction($options);
			$this->logger->addDebug(100, $gcLinkResponse);
			if ($gcLinkResponse[0] != 1) {
				//$this->logger->addDebug($orderIncrementId . ' gift card failure with ' . $giftcardNumber . ' because ' . $gcLinkResponse[3]);
				throw new LocalizedException(__('The gift card you applied no longer has a valid balance or there was an error in processing. Please remove the gift card and try again.'));
			} else {
				return $this;
			}
		}
    }
}
