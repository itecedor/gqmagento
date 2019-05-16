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

use Magento\Customer\Controller\Adminhtml\Group;

/**
 * Class Delete
 * @package Ced\Etsy\Controller\Adminhtml\Profile
 */
class Delete extends Group
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ced_Etsy::profile';

    /**
     * Delete the Attribute
     */
    public function execute()
    {
        $code = $this->getRequest()->getParam('pcode');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($code) {
            $model = $this->_objectManager->create('Ced\Etsy\Model\Profile')->getCollection()->addFieldToFilter('profile_code', $code);

            // entity type check
            try {
                foreach ($model as $value) {
                    if($code == $value->getData('profile_code')) {
                        $value->delete();
                    }
                }
                $this->messageManager->addSuccessMessage(__('You deleted the profile.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath(
                    'etsy/profile/edit',
                    ['pcode' => $this->getRequest()->getParam('pcode')]
                );         
                //End
            }
        }
        $this->_redirect('etsy/profile/index');
          return ;
    }
}

