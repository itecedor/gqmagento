<?php

namespace POSIMWebExt\WebExtManager\Controller\Adminhtml\Update;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Block\Page;
use Magento\Framework\View\Result\PageFactory;
use POSIMWebExt\WebExtManager\Helper\Data;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected $webextHelper;

    protected $resultFactory;

    /**
     * @param Data $helper
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */

    public function __construct(
        Data $helper,
        Context $context,
        PageFactory $resultPageFactory
    ){
        parent::__construct($context);
        $this->resultFactory = $context->getResultFactory();
        $this->resultPageFactory = $resultPageFactory;
        $this->webextHelper = $helper;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $extToUpdate = $this->getRequest()->getParam('ext');
        $version = $this->webextHelper->getAvailableVersion($extToUpdate);
        $success = $this->webextHelper->updateExtension($extToUpdate, $version);
        if($success === false)
        {
            die('fail');
        }else{
            $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);
            return $redirect->setController('installed')->forward('index');
        }

    }
}