<?php

namespace POSIMWebExt\GCLink\Block\Sales\Order;



class Posimgiftcard extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_config;

    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

    protected $gcHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
        \POSIMWebExt\GCLink\Helper\Data $gcHelper,
        array $data = []
    ) {
        $this->_config = $taxConfig;
        $this->gcHelper = $gcHelper;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return bool
     */
    public function displayFullSummary()
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->_source;
    }
    public function getStore()
    {
        return $this->_order->getStore();
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * @return $this
     */
    public function initTotals()
    {

        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        $store = $this->getStore();

        $posimgiftcard = new \Magento\Framework\DataObject(
            [
                'code' => 'posimgiftcard',
                'strong' => false,
                'value' => 0,
                'label' => __('Gift Card'),
            ]
        );

        $parent->addTotal($posimgiftcard, 'posimgiftcard');
        $parent->addTotal($posimgiftcard, 'posimgiftcard');


        return $this;
    }

}
