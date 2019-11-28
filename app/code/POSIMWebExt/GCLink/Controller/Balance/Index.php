<?php

namespace POSIMWebExt\GCLink\Controller\Balance;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $pageFactory;

    public function __construct(Context $context, PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $page_object = $this->pageFactory->create();

        return $page_object;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('POSIMWebExt_GCLink::cards');
    }
}