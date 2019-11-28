<?php

namespace POSIMWebExt\GCLink\Plugin;

class OrderApi
{
    protected $logger;
    protected $orderExtensionFactory;
    protected $serializer;
    protected $cardsInterface;
    protected $orderItemExtensionFactory;

    /**
     * OrderApi constructor.
     *
     * @param \POSIMWebExt\WebExtManager\Logger\Logger $logger
     */
    public function __construct(\POSIMWebExt\GCLink\Logger\Logger $logger,
                                \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory,
                                \POSIMWebExt\GCLink\Api\Data\CardsInterface $cardsInterface,
                                \Magento\Framework\Serialize\SerializerInterface $serializer,
                                \Magento\Sales\Api\Data\OrderItemExtensionFactory $orderItemExtensionFactory
    )
    {
        $this->logger = $logger;
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->cardsInterface = $cardsInterface;
        $this->serializer = $serializer;
        $this->orderItemExtensionFactory = $orderItemExtensionFactory;
    }

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface      $entity
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public
    function afterGetList(\Magento\Sales\Api\OrderRepositoryInterface $subject,
                          \Magento\Sales\Model\ResourceModel\Order\Collection $entity)
    {
        foreach ($entity as $order) {
            $extensionAttributes = $order->getExtensionAttributes();
            if ($extensionAttributes && $extensionAttributes->getGiftcardInformation()) {
                continue;
            }
            if ($order->getData('posimgiftcard') && $order->getData('posimgc_num') && $order->getData('posimgiftcard') > 0) {
                //only add data if it exists
                $extensionAttributes->setGiftcardInformation(array(array('giftcard_discount' => $order->getData('posimgiftcard'), 'posimgc_num' => $order->getData('posimgc_num'))));
            }
            $orderItems = $order->getItems();
            if ($orderItems) {
                foreach ($orderItems as $item) {
                    if ($item->getProductType() == \POSIMWebExt\GCLink\Model\Product\Type\Giftcard::TYPE_GIFTCARD) {
                        $itemExtension = $item->getExtensionAttributes();
                        if ($itemExtension && $itemExtension->getGiftcardInformation()) {
                            continue;
                        }
                        $this->logger->addDebug('we got one!');
                        $giftcard = $item->getProductOptionByCode('additional_options');
                        $this->logger->addDebug('GC: ', $giftcard);
                        $this->logger->addDebug('woooot');
                        $orderItemExtension = $itemExtension
                            ? $itemExtension
                            : $this->orderItemExtensionFactory->create();
                        $orderItemExtension->setGiftcardInformation($giftcard);
                        $item->setExtensionAttributes($orderItemExtension);
                        $this->logger->addDebug(100, $orderItemExtension->getGiftcardInformation());
                        $this->logger->addDebug(100, $giftcard);
                        $this->logger->addDebug($order->getData('posimgc_num'));
                    }
                }
            }
        }

        return $entity;
    }
}
