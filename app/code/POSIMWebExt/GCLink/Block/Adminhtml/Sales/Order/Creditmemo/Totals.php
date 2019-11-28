<?php

namespace POSIMWebExt\GCLink\Block\Adminhtml\Sales\Order\Creditmemo;

use Magento\Framework\View\Element\Template;

class Totals extends \Magento\Framework\View\Element\Template
{
    protected $creditMemo = null;

    protected $source;

    public function __construct(Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function getCreditMemo()
    {
        return $this->getParentBlock()->getCreditmemo();
    }

    public function initTotals()
    {
        $this->getParentBlock();
        $this->getCreditMemo();
        $this->getSource();

        $posimgiftcard = new \Magento\Framework\DataObject(
            [
                'code'  =>  'posimgiftcard',
                'strong'    =>  'false',
                'value' =>  $this->getSource()->getPosimgiftcard(),
                'label' =>  'Gift Card'
            ]
        );

        $this->getParentBlock()->addTotalBefore($posimgiftcard, 'grand_total');

        return $this;
    }
}