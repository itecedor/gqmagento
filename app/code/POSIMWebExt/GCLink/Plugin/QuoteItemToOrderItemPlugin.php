<?php

namespace POSIMWebExt\GCLink\Plugin;

use Countable;
use \POSIMWebExt\GCLink\Model\Product\Type\Giftcard;

class QuoteItemToOrderItemPlugin
{
    protected $serializer;
    protected $logger;
    protected $helper;

    public function __construct(\Magento\Framework\Serialize\SerializerInterface $serializer,
								\POSIMWebExt\GCLink\Logger\Logger $logger,
                                \POSIMWebExt\GCLink\Helper\Data $helper
	
	)
    {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->helper = $helper;
	}

    public function aroundConvert(\Magento\Quote\Model\Quote\Item\ToOrderItem $subject, callable $proceed, $quoteItem, $data)
    {
        $orderItem = $proceed($quoteItem, $data);
        if ($orderItem->getProductType() == Giftcard::TYPE_GIFTCARD) {

            if ($additionalOptionsQuote = $quoteItem->getOptionByCode('additional_options') instanceof Countable) {
                if ($additionalOptionsOrder = $orderItem->getProductOptionByCode('additional_options') instanceof Countable) {
                    $additionalOptions = array_merge($additionalOptionsQuote, $additionalOptionsOrder);
                } else {
                    $additionalOptions = $additionalOptionsQuote;
                }
                if (count($additionalOptions) > 0) {
					$this->logger->addDebug(100, $additionalOptions);
                    $options = $orderItem->getProductOptions();
                    $options['additional_options'] = $this->serializer->unserialize($additionalOptions->getValue());
                    $orderItem->setProductOptions($options);
                }
            }
        }

        return $orderItem;
    }
}