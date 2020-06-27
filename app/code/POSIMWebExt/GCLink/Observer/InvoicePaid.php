<?php

namespace POSIMWebExt\GCLink\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class InvoicePaid implements ObserverInterface
{
    protected $gcHelper;
    protected $orderItemExtensionFactory;
    protected $logger;

    public function __construct(\POSIMWebExt\GCLink\Helper\Data $gclinkHelper,
                                \Magento\Sales\Api\Data\OrderItemExtensionFactory $orderItemExtensionFactory,
								\POSIMWebExt\GCLink\Logger\Logger $logger
	)
    {
        $this->gcHelper = $gclinkHelper;
        $this->orderItemExtensionFactory = $orderItemExtensionFactory;
        $this->logger = $logger;
	}

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $amount = 0;
        $invoice = $observer->getEvent()->getInvoice();
        $invoiceItems = $invoice->getItemsCollection();
        foreach ($invoiceItems as $item) {
            if ($item->getOrderItem()->getProductType() == 'posimgc') {
				$this->logger->addDebug("product name: " . $item->getOrderItem()->getName());
				$this->logger->addDebug('options', $item->getOrderItem()->getProductOptions());

                $options = $item->getOrderItem()->getProductOptions();
				$this->logger->addDebug('options variable', $options);

                //$additionalOptions = $options['additional_options'];

				$additionalOptions = isset($options['additional_options']) ? $options['additional_options'] : null;
				$this->logger->addDebug('additional_options', $options['additional_options']);

				if (is_array($additionalOptions))
				{
					$this->logger->addDebug('additional options', $additionalOptions);
					$this->logger->addDebug('product options from invoice', $item->getOrderItem()->getProductOptions());
					$this->logger->addDebug('options', $options);
				}
				else
				{
					$this->logger->addDebug('$additionalOptions not an array.');
				}

                foreach ($additionalOptions as $option) {
                    if ($option['label'] == 'amount') {
                        $amount = $option['value'];
						$this->logger->addDebug($amount);
                    }
                    if ($option['label'] == 'email') {
                        $gcRecipEmail = $option['value'];
						$this->logger->addDebug($gcRecipEmail);
                    }
                    if ($option['label'] == 'type') {
                        $gcType = $option['value'];
						$this->logger->addDebug($gcType);
                    }
                    if ($option['label'] == 'recip_name') {
                        $gcRecipName = $option['value'];
						$this->logger->addDebug($gcRecipName);
                    }
                    if ($option['label'] == 'gc_giftmessage') {
                        $gcGiftMessage = $option['value'];
						$this->logger->addDebug($gcGiftMessage);
                    }
                }
                if ($amount != 0 && isset($gcType) && $gcType == 'virtual') {
                    $newCardNumber = $this->gcHelper->getNextCardNum();
                    $options = array(
                        'x_Card_Num'    => urlencode($newCardNumber),
                        'x_Type'        => urlencode('SL'),
                        'x_Invoice_Num' => urlencode($invoice->getOrder()->getIncrementId()),
                        'x_Amount'      => urlencode($amount)
                    );
                    $success = $this->gcHelper->postGCLinkTransaction($options);
                    if ($success[0] != 1) {
                        throw new LocalizedException(__('Gift card purchase failed.'));
                    } else {
                        //TODO: send announcement email when virt is purchased
                        $gcRecipName = isset($gcRecipName) ? $gcRecipName : 'Gift Card Recipient';
                        $gcGiftMessage = isset($gcGiftMessage) ? $gcGiftMessage : 'Enjoy your gift!';
                        $this->gcHelper->sendActivatedEmail($amount, $newCardNumber, $gcRecipEmail, $gcRecipName, $gcGiftMessage, $invoice->getOrder()->getCustomerEmail());
                        $additionalOptions[] = array('label' => 'posimgc_num', 'value' => $newCardNumber);
                        $options['additional_options'] = $additionalOptions;
                        $item->getOrderItem()->setProductOptions($options);
                        $orderItem = $item->getOrderItem();
                        $extensionAttributes = $orderItem->getExtensionAttributes();
                        $orderItemExtension = $extensionAttributes
                            ? $extensionAttributes
                            : $this->orderItemExtensionFactory->create();
                        $orderItemExtension->setGiftcardInformation($additionalOptions);
                        $orderItem->setExtensionAttributes($orderItemExtension);
                        $item->setGiftcardInformation($additionalOptions);
                    }
                } else {
                    throw new LocalizedException(__('Gift Card purchased amount was null...'));
                }
            }
        }
    }
}
