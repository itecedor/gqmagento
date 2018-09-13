<?php

namespace POSIMWebExt\WebExtManager\Controller\Adminhtml\Installed;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Block\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
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
        PageFactory $resultPageFactory
    ){
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('POSIMWebExt_WebExtManager::installed');
        $resultPage->getConfig()->getTitle()->prepend(__('POSIM Extensions'));


        return $resultPage;
    }
}