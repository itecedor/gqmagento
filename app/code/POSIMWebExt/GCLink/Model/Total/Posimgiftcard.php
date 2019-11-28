<?php

namespace POSIMWebExt\GCLink\Model\Total;

use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use POSIMWebExt\GCLink\Helper\Data as GiftcardHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Posimgiftcard extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \POSIMWebExt\GCLink\Helper\Data
     */
    protected $gcHelper;
    protected $logger;
    protected $gcMath;
    protected $priceCurrency;

    public function __construct(\POSIMWebExt\GCLink\Logger\Logger $logger,
                                GiftcardHelper $gclinkHelper
    )
    {
        $this->gcHelper = $gclinkHelper;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     *
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }
        parent::collect($quote, $shippingAssignment, $total);
        $giftcard = (float)$quote->getPosimgiftcard();
        $total->setTotalAmount('posimgiftcard', $giftcard);
        $total->setBaseTotalAmount('posimgiftcard', $giftcard);
        $total->setPosimgiftcard($giftcard);
        $total->setBasePosimgiftcard($giftcard);
        $quote->setPosimgiftcard($giftcard);
        $quote->setBasePosimgiftcard($giftcard);
        $quote->setGrandTotal($total->getGrandTotal() + $giftcard);
        $quote->setBaseGrandTotal($total->getBaseGrandTotal() + $giftcard);

        return $this;
    }

    /**
     * @param Address\Total $total
     */
    public function clearValues(Address\Total $total, \Magento\Quote\Model\Quote $quote)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
        $total->setPosimgiftcard(0);
        $total->setBasePosimgiftcard(0);
        $quote->setBasePosimgiftcard(0);
        $quote->setPosimgiftcard(0);
    }

    /**
     * @param \Magento\Quote\Model\Quote               $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return [
            'code'  => 'posimgiftcard',
            'title' => 'Gift Card',
            'value' => $quote->getPosimgiftcard()
        ];
    }

    public function getLabel()
    {
        return __('Gift Card');
    }
}