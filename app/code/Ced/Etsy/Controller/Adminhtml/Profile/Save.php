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
use Magento\Backend\App\Action;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ced_Etsy::profile';
    public $_coreRegistry;
    public $_cache;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Ced\Etsy\Helper\Cache $cache
    )
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_cache = $cache;
    }

    /**
     *
     * @param string $idFieldName
     * @return mixed
     */
    protected function _initProfile($idFieldName = 'pcode')
    {

        $profileCode = $this->getRequest()->getParam($idFieldName);
        $profile = $this->_objectManager->get('Ced\Etsy\Model\Profile');

        if ($profileCode) {
            $profile->loadByField('profile_code', $profileCode);
        }

        $this->getRequest()->setParam('is_etsy', 1);
        $this->_coreRegistry->register('current_profile', $profile);
        return $this->_coreRegistry->registry('current_profile');
    }

    /**
     *
     */
    public function execute()
    {
        $optAttribute = $etsyAttribute = $etsyReqOptAttribute = [];
        $data = $this->_objectManager->create('Magento\Config\Model\Config\Structure\Element\Group')->getData();
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_context = $this->_objectManager->get('Magento\Framework\App\Helper\Context');
        $redirectBack = $this->getRequest()->getParam('back', false);
        $tab = $this->getRequest()->getParam('tab', false);
        $pcode = $this->getRequest()->getParam('pcode', false);
        $profileData = $this->getRequest()->getPostValue();
        $category[] = isset($profileData['level_0']) ? $profileData['level_0'] : "";
        $category[] = isset($profileData['level_1']) ? $profileData['level_1'] : "";
        $category[] = isset($profileData['level_2']) ? $profileData['level_2'] : "";
        $category[] = isset($profileData['level_3']) ? $profileData['level_3'] : "";
        $category[] = isset($profileData['level_4']) ? $profileData['level_4'] : "";
        $category[] = isset($profileData['level_5']) ? $profileData['level_5'] : "";
        $category[] = isset($profileData['level_6']) ? $profileData['level_6'] : "";

        $profileData = json_decode(json_encode($profileData), 1);

        $inProfile = $this->getRequest()->getParam('in_profile');
        $profileProducts = $this->getRequest()->getParam('in_profile_product', null);
        parse_str($profileProducts, $profileProducts);
        $profileProducts = array_keys($profileProducts);
        $oldProfileProducts = $this->getRequest()->getParam('in_profile_product_old');
        parse_str($oldProfileProducts, $oldProfileProducts);
        $oldProfileProducts = array_keys($oldProfileProducts);

        $profileData = json_decode(json_encode($profileData), 1);

        $resource = $this->getRequest()->getPost('resource', false);

        try {
            $profile = $this->_initProfile('pcode');
            if (!$profile->getId() && $pcode) {
                $this->messageManager->addErrorMessage(__('This Profile no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            $pname = $profileData['profile_name'];
            if (isset($profileData['profile_code'])) {
                $pcode = $profileData['profile_code'];
                $profileCollection = $this->_objectManager->get('Ced\Etsy\Model\Profile')->getCollection()
                    ->addFieldToFilter('profile_code', $profileData['profile_code']);
                if (count($profileCollection) > 0) {
                    $this->messageManager->addErrorMessage(__('This Profile Already Exist Please Change Profile Code'));
                    $this->_redirect('*/*/new');
                    return;
                }
            }

            // check for category save
            if (empty($pcode)) {
                $checkCategory = $this->_objectManager->get('Ced\Etsy\Model\Profile')->getCollection()->addFieldToFilter('profile_category', json_encode($category));
                if ($checkCategory->getSize() > 0) {
                    $this->messageManager->addErrorMessage(__('Category Already Exist For Other Profile Please Change Category'));
                    $this->_redirect('*/*/new');
                    return;
                }
            } else {
                $checkCategory = $this->_objectManager->get('Ced\Etsy\Model\Profile')->getCollection()->addFieldToFilter('profile_category', json_encode($category))->getData();
                foreach ($checkCategory as $value) {
                    if ($value['profile_code'] != $pcode) {
                        $this->messageManager->addErrorMessage(__('Category Already Exist For Other Profile Please Change Category'));
                        $this->_redirect('*/*/edit', ['pcode' => $pcode]);
                        return;
                    }
                }
            }
            $profile->addData($profileData);
            $profile->setProfileCategory(json_encode($category));

            // save required attribute
            if (!empty($profileData['required_attributes'])) {
                $temAttribute = $this->unique_multidim_array(
                    $profileData['required_attributes'],
                    'etsy_attribute_name'
                );
                $temp1 = [];
                foreach ($temAttribute as $item) {
                    if ($item['required']) {
                        $temp1['etsy_attribute_name'] = $item['etsy_attribute_name'];
                        $temp1['etsy_attribute_type'] = $item['etsy_attribute_type'];
                        $temp1['magento_attribute_code'] = $item['magento_attribute_code'];
                        $temp1['required'] = $item['required'];
                        $reqAttribute[] = $temp1;
                    }
                }
                $etsyReqOptAttribute['required_attributes'] = $reqAttribute;

                $profile->setProfileReqOptAttribute(json_encode($etsyReqOptAttribute));
            } else {
                $profile->setProfileReqOptAttribute('');
            }

            // save category recipient
            if (isset($profileData['recipient'])) {
                $profile->setRecipient($profileData['recipient']);
            }
            // save category occasion
            if (isset($profileData['occasion'])) {
                $profile->setOccasion($profileData['occasion']);
            }
            // save category tags
            if (isset($profileData['tags'])) {
                $profile->setTags($profileData['tags']);
            }

            //save profile
            $profile->save();

            //cache values
            $this->_cache->setValue(\Ced\Etsy\Helper\Cache::PROFILE_CACHE_KEY . $profile->getId(), $profile->getData());

            foreach ($oldProfileProducts as $oUid) {
                $this->_deleteProductFromProfile($oUid);
                $this->_cache->removeValue(\Ced\Etsy\Helper\Cache::PROFILE_PRODUCT_CACHE_KEY . $oUid);
            }

            foreach ($profileProducts as $nRuid) {
                $this->_addProductToProfile($nRuid, $profile->getId());
                $this->_cache->setValue(\Ced\Etsy\Helper\Cache::PROFILE_PRODUCT_CACHE_KEY . $nRuid, $profile->getId());
            }

            if ($redirectBack && $redirectBack == 'edit') {
                $this->messageManager->addSuccessMessage(
                    __(
                        '
		   		You Saved The Etsy Profile And Its Products.
		   			'
                    )
                );
                $this->_redirect(
                    '*/*/edit',
                    [
                        'pcode' => $pcode,
                    ]
                );
            } elseif ($redirectBack && $redirectBack == 'upload') {
                $this->messageManager->addSuccessMessage(
                    __(
                        '
		   		You Saved The Etsy Profile And Its Products. Upload Product Now.
		   			'
                    )
                );
                $this->_redirect(
                    'etsy/products/index',
                    [
                        'profile_id' => $profile->getId()
                    ]
                );
            } else {
                $this->messageManager->addSuccessMessage(
                    __(
                        '
		   		You Saved The Etsy Profile And Its Products.
		   		'
                    )
                );
                $this->_redirect('*/*/');
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __(
                    '
		   		Unable to Save Profile Please Try Again.
		   			' . $e->getMessage()
                )
            );
            $this->_redirect(
                '*/*/edit',
                ['pcode' => $pcode]
            );
        }

        return;
    }

    /**
     * @param $productId
     * @param $profileId
     * @return bool
     */
    public function _addProductToProfile($productId, $profileId)
    {
        $profileproduct = $this->_objectManager->create("Ced\Etsy\Model\Profileproducts")
            ->deleteFromProfile($productId);

        if ($profileproduct->profileProductExists($productId, $profileId) === true) {
            return false;
        } else {
            $profileproduct->setProductId($productId);
            $profileproduct->setProfileId($profileId);
            $profileproduct->save();
            return true;
        }
    }

    /**
     * @param $productId
     * @return bool
     * @throws \Exception
     */
    public function _deleteProductFromProfile($productId)
    {
        try {
            $this->_objectManager->create("Ced\Etsy\Model\Profileproducts")
                ->deleteFromProfile($productId);
        } catch (\Exception $e) {
            throw $e;
            return false;
        }
        return true;
    }

    /**
     * @param $array
     * @param $key
     * @return array
     */
    public function unique_multidim_array($array, $key)
    {
        $temp_array = [];
        $i = 0;
        $key_array = [];

        foreach ($array as $val) {
            if ($val['delete'] == 1) {
                continue;
            }

            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }
}
