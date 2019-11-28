<?php

namespace POSIMWebExt\GCLink\Plugin;

use \POSIMWebExt\GCLink\Model\Product\Type\Giftcard;

class QuoteItemToOrderItemPlugin
{
    protected $serializer;

    public function __construct(\Magento\Framework\Serialize\SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function aroundConvert(\Magento\Quote\Model\Quote\Item\ToOrderItem $subject, callable $proceed, $quoteItem, $data)
    {

        $orderItem = $proceed($quoteItem, $data);
        if ($orderItem->getProductType() == Giftcard::TYPE_GIFTCARD) {

            if ($additionalOptionsQuote = $quoteItem->getOptionByCode('additional_options')) {
                if ($additionalOptionsOrder = $orderItem->getProductOptionByCode('additional_options')) {
                    $additionalOptions = array_merge($additionalOptionsQuote, $additionalOptionsOrder);
                } else {
                    $additionalOptions = $additionalOptionsQuote;
                }
                if (count($additionalOptions) > 0) {
                    $options = $orderItem->getProductOptions();
                    $options['additional_options'] = $this->serializer->unserialize($additionalOptions->getValue());
                    $orderItem->setProductOptions($options);
                }
            }
        }

        return $orderItem;
    }
}