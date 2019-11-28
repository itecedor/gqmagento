<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Requestforquote
* @copyright   Copyright (c) 2016 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extensions/request-for-quote-m2.html
*
**/
namespace Milople\Requestforquote\Controller\Adminhtml\Enquiries;

use Magento\Backend\App\Action\Context;
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
    ) {
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
       
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Milople_Requestforquote::enquiries');
        $resultPage->addBreadcrumb(__('Enquiries'), __('Enquiries'));
        $resultPage->addBreadcrumb(__('Milople Request For Quote Enquiries'), __('Milople Request For Quote Enquiries'));
        $resultPage->getConfig()->getTitle()->prepend(__('Milople Request For Quote Enquiries'));

        return $resultPage;
    }

    /**
     * Is the user allowed to view the fonts grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}