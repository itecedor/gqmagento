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

class MassDisable extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ced_Etsy::profile';

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {

        $profileIds = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded', false);
        if (!is_array($profileIds) && !$excluded) {
            $this->messageManager->addErrorMessage(__('Please select Profile(s).'));
        } else if($excluded == "false") {
            $profileIds  = $this->_objectManager->create('Ced\Etsy\Model\Profile')->getCollection()->getAllIds();
        }


        if (!empty($profileIds)) {
            try {
                foreach ($profileIds as $profileId) {
                    $profile = $this->_objectManager->create('Ced\Etsy\Model\Profile')->load($profileId);
                    $profile->setProfileStatus(0);
                    $profile->save();
                }
                $this->messageManager->addSuccessMessage(__('Total of %1 record(s) have been disabled.', count($profileIds)));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }
}