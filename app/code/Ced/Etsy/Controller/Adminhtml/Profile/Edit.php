<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_Etsy
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Etsy\Controller\Adminhtml\Profile;
 
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
 
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ced_Etsy::profile';
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_entityTypeId;
    protected $_coreRegistry;
    
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    
    
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry     = $coreRegistry;
        $this->scopeConfigManager = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->_token = $this->scopeConfigManager->getValue('etsy_config/etsy_setting/token');
    }
    /**
     * Index action
     *
     * @return void
     */
    
    public function execute()
    {
        $profileCode = $this->getRequest()->getParam('pcode');
        if($profileCode) {
            $profile = $this->_objectManager->create('Ced\Etsy\Model\Profile')->getCollection()->addFieldToFilter('profile_code', $profileCode)->getFirstItem();
            $this->getRequest()->setParam('is_profile', 1);
            $this->_coreRegistry->register('current_profile', $profile);

            if ($profile->getId() && !empty($profile)) {
                $breadCrumb      = __('Edit Profile');
                $breadCrumbTitle = __('Edit Profile');
            } else {
                $breadCrumb = __('Add New Profile');
                $breadCrumbTitle = __('Add New Profile');
            }
            $item=$profile->getId() ? $profile->getProfileName() : __('New Profile');
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend($profile->getId() ? $profile->getProfileName() : __('New Profile'));
            $resultPage->getLayout()
                ->getBlock('profile_edit_js')
                ->setIsPopup((bool)$this->getRequest()->getParam('popup'));
            return $resultPage;
        }
        
        else
        {

            $profile = $this->_objectManager->create('Ced\Etsy\Model\Profile');
            $this->_coreRegistry->register('current_profile', $profile);


            $breadCrumb = __('Add New Profile');
            $breadCrumbTitle = __('Add New Profile');
            $item= __('New Profile');
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(__('New Profile'));
            $resultPage->getLayout()
                ->getBlock('profile_edit_js')
                ->setIsPopup((bool)$this->getRequest()->getParam('popup'));
            return $resultPage;
        }
    }   
}