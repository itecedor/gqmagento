<?php

namespace Lof\AffiliateSaveCart\Controller\Cart;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Lof\AffiliateSaveCart\Controller\AbstractCart;

class Edit extends AbstractCart
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $customerSession);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Edit Cart'));
        $this->setActiveNav($resultPage);
        return $resultPage;
    }
}
