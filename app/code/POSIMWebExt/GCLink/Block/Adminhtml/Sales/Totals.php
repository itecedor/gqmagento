<?php

namespace POSIMWebExt\GCLink\Block\Adminhtml\Sales;

/**
 * Class Totals
 * @package POSIMWebExt\GCLink\Block\Adminhtml\Sales
 */
class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \POSIMWebExt\GCLink\Helper\Data
     */
    protected $gcHelper;
    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \POSIMWebExt\GCLink\Helper\Data $helper
     * @param \Magento\Directory\Model\Currency $currency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \POSIMWebExt\GCLink\Helper\Data $helper,
        \Magento\Directory\Model\Currency $currency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->gcHelper = $helper;
        $this->currency = $currency;
    }

    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function getCurrencySymbol()
    {
        return $this->currency->getCurrencySymbol();
    }


    public function initTotals()
    {
        $this->getParentBlock();
        $this->getOrder();
        $this->getSource();

        if(!$this->getSource()->getPosimgiftcard()) {
            return $this;
        }
        $total = new \Magento\Framework\DataObject(
            [
                'code' => 'posimgiftcard',
                'value' => $this->getSource()->getPosimgiftcard(),
                'label' => 'Gift Card',
            ]
        );
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');

        return $this;
    }
}
