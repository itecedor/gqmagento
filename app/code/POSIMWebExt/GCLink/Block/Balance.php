<?php

namespace POSIMWebExt\GCLink\Block;

use \Magento\Framework\View\Element\Template;
use \POSIMWebExt\GCLink\Helper\Data;

class Balance extends Template
{
    protected $request;
    protected $gcHelper;

    public function __construct(Data $gcHelper, Template\Context $context, array $data = [])
    {
        $this->request = $context->getRequest();
        $this->gcHelper = $gcHelper;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $cardNum = $this->request->getPost('cardnum');
        $this->setCardnum($cardNum);
        $balance = $this->gcHelper->getCardBalance($cardNum);
        $this->setBalance($balance);
    }
}