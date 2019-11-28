<?php

namespace POSIMWebExt\GCLink\Block\Adminhtml\Sales\Order\Invoice;

use Magento\Framework\View\Element\Template;

class Totals extends \Magento\Framework\View\Element\Template
{
    protected $gcHelper;
    protected $invoice = NULL;
    protected $source;

    public function __construct(Template\Context $context, \POSIMWebExt\GCLink\Helper\Data $helper, array $data = [])
    {
        $this->gcHelper = $helper;
        parent::__construct($context, $data);
    }

    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
    }

    public function initTotals()
    {
        $this->getParentBlock();
        $this->getInvoice();
        $this->getSource();


        if(!$this->getSource()->getPosimgiftcard()) {
            return $this;
        }
        $total = new \Magento\Framework\DataObject(
            [
                'code'  =>  'posimgiftcard',
                'value' =>  $this->getSource()->getPosimgiftcard(),
                'label' =>  'Gift Card',
            ]
        );

        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        return $this;
    }
}