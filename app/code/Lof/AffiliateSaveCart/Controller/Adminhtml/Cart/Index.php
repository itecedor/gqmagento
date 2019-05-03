<?php

namespace Lof\AffiliateSaveCart\Controller\Adminhtml\Cart;
use Magento\Backend\App\Action\Context;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context
     * @param \Magento\Framework\View\Result\PageFactory
     * @param \Magento\Framework\Registry
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_AffiliateSaveCart::cart');
    }

    public function execute()
    {

        $resultPage = $this->resultPageFactory->create();

        /**
         * Set active menu item
         */
        $resultPage->setActiveMenu("Lof_AffiliateSaveCart::save_cart");
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Save Cart'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('Lof_AffiliateSaveCart'),__('Manage Save Cart'));
        $resultPage->addBreadcrumb(__('Manage Save Cart'),__('Manage Save Cart'));

        return $resultPage;
    }

}