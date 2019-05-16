<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_Etsy
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Etsy\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Message\Manager;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Backend\Model\Session;
use Magento\Framework\Filesystem;

/**
 * Class Etsy
 * @package Ced\Etsy\Helper
 */
class Etsy extends AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * @var Session
     */
    public $adminSession;
    /**
     * @var Manager
     */
    public $messageManager;
    /**
     * DirectoryList
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    public $directoryList;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $json;
    /**
     * @var Curl
     */
    public $_resource;
    /**
     * @var string
     */
    public $consumerkey;
    /**
     * @var string
     */
    public $consumersecretkey;
    /**
     * @var string
     */
    public $tokensecret;
    /**
     * @var string
     */
    public $token;
    /**
     * @var string
     */
    public $accesstoken;
    /**
     * @var string
     */
    public $accesstokensecret;
    /**
     * @var string
     */
    public $country;
    /**
     * @var string
     */
    public $storeid;
    /**
     * @var Filesystem
     */
    public $filesystem;

    /**
     * Etsy constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param Manager $manager
     * @param DirectoryList $directoryList
     * @param Data $json
     * @param Curl $curl
     * @param Session $session
     * @param Filesystem $filesystem ,
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        Manager $manager,
        DirectoryList $directoryList,
        \Magento\Framework\Json\Helper\Data $json,
        Curl $curl,
        Session $session,
        Filesystem $filesystem
    )
    {
        $this->objectManager = $objectManager;
        $this->_resource = $curl;
        parent::__construct($context);
        $this->messageManager = $manager;
        $this->directoryList = $directoryList;
        $this->json = $json;
        $this->adminSession = $session;
        $this->filesystem = $filesystem;
        $this->consumerkey = $this->scopeConfig->getValue('etsy_config/etsy_setting/consumer_key');
        $this->consumersecretkey = $this->scopeConfig->getValue('etsy_config/etsy_setting/consumer_secret_key');
        $this->accesstoken = $this->scopeConfig->getValue('etsy_config/etsy_setting/access_token');
        $this->accesstokensecret = $this->scopeConfig->getValue('etsy_config/etsy_setting/access_token_secret');
        $this->country = $this->scopeConfig->getValue('etsy_config/etsy_setting/country');
        $this->storeid = $this->scopeConfig->getValue('etsy_config/etsy_setting/storeid');
    }

    /**
     * @param $productId
     * @return array
     */
    public function prepareData($productId)
    {
        try {
            $whoMade = $this->scopeConfig->getValue('etsy_config/product_setting/who_made');
            $whenMade = $this->scopeConfig->getValue('etsy_config/product_setting/when_made');
            $state = $this->scopeConfig->getValue('etsy_config/product_setting/state');
            $shopName = $this->scopeConfig->getValue('etsy_config/etsy_setting/shop_name');
            $shippingTemplateId = $this->scopeConfig->getValue('etsy_config/shipping_details/shipping_template');

            if (empty($shopName) || empty($shippingTemplateId) || empty($whoMade) || empty($whenMade) || empty($state)) {
                $content = [
                    'type' => 'error',
                    'data' => 'Please fill the configuration section for Etsy.'
                ];
                return $content;
            }
            $folderPath = $this->filesystem->getDirectoryRead(DirectoryList::APP)
                ->getAbsolutePath('code/Ced/Etsy/Setup/json/');
            $path = $folderPath . 'ShippingTemplate.json';
            if (file_exists($path)) {
                $shippingTemplates = file_get_contents($path);
                if ($shippingTemplates != '') {
                    $shippingTemplates = json_decode($shippingTemplates, true);
                }
                foreach ($shippingTemplates as $value) {
                    if ($value['shipping_template_id'] == $shippingTemplateId) {
                        $processingDaysMin = $value['min_processing_days'];
                        $processingDaysMax = $value['min_processing_days'];
                        $userId = $value['user_id'];
                        $proDaysDisplaylabel = $value['processing_days_display_label'];
                        $originCountryId = $value['origin_country_id'];
                        break;
                    }
                }
            }
            $product = $this->objectManager->create('Magento\Catalog\Model\Product')->load($productId);
            $profileId = $this->objectManager->get('Ced\Etsy\Model\Profileproducts')->loadByField('product_id', $productId);
            $profileData = $this->objectManager->get('Ced\Etsy\Model\Profile')->load($profileId->getProfileId());
            $catJson = $profileData->getProfileCategory();
            $primarycatId = "";
            if ($catJson) {
                $catArray = array_reverse(json_decode($catJson, true));
                foreach ($catArray as $value) {
                    if ($value != "") {
                        $primarycatId = $value;
                        break;
                    }
                }
            }
            $primarycatArray = explode('.', $primarycatId);
            $primarycatId = $primarycatArray[0];
            $reqOptAttr = $profileData->getProfileReqOptAttribute();
            $itemData1 = $this->reqOptAttributeData($product, json_decode($reqOptAttr, true));
            if (isset($itemData1['type'])) {
                $content = [
                    'type' => 'error',
                    'data' => $itemData1['data']
                ];
                return $content;
            }
            $shopSectionId = $this->objectManager->get('Ced\Etsy\Helper\Data')->ApiObject()->findAllShopSections(['params' => ['shop_id' => $shopName]]);
            $tagString = $profileData->getTags();
            $alltags = explode(',', $tagString);
            $count = 1;
            foreach ($alltags as $value) {
                $tags[] = substr($value, 0, 20);
                $count++;
                if ($count > 12) {
                    break;
                }
            }

            $itemData2 = [
                'materials' => ['leather'],
                'shipping_template_id' => (int)$shippingTemplateId,
                'shop_section_id' => $shopSectionId['results'][0]['shop_section_id'],
                /*'image_ids' => '',
                'is_customizable' => false,*/
                'non_taxable' => true,
                'state' => $state,
                'processing_min' => $processingDaysMin,
                'processing_max' => $processingDaysMax,
                /*'category_id' => $primarycatId,*/
                'taxonomy_id' => (int)$primarycatId,
                'tags' => $tags,
                'who_made' => $whoMade,
                'is_supply' => true,
                'when_made' => $whenMade,
                'recipient' => $profileData->getRecipient(),
                'occasion' => $profileData->getOccasion(),
                'style' => [],
            ];
            $itemData = array_merge_recursive($itemData1,$itemData2);
            $content = [
                'type' => 'success',
                'data' => $itemData
            ];
        } catch (\Exception $e) {
            $content = [
                'type' => 'success',
                'data' => $e->getMessage()
            ];
        }
        return $content;
    }

    /**
     * @param $product
     * @param $reqOptAttr
     * @return array
     */
    public function reqOptAttributeData($product, $reqOptAttr)
    {
        $item = [];
        $error = false;
        $msg = "";
        foreach ($reqOptAttr['required_attributes'] as $value) {
            switch ($value['etsy_attribute_name']) {
                case 'name':
                    $item['title'] = $product->getData($value['magento_attribute_code']);
                    if (empty($item['title'])) {
                        $error = true;
                        $msg = "title is missing";
                    }
                    break;
                /*case 'sku':
                    $item['sku'] = $product->getData($value['magento_attribute_code']);
                    if (empty($item['sku'])) {
                        $error = true;
                        $msg = "SKU is missing";
                    }
                    break;*/
                case 'price':
                    $item['price'] = $product->getData($value['magento_attribute_code']) != '' ? $product->getData($value['magento_attribute_code']) : ''; //(float)$product->getFinalPrice()
                    if (empty($item['price']) || !is_numeric($data['price'])) {
                        $error = true;
                        $msg = "Price should be numeric";
                    }
                    break;
                case 'description':
                    $item['description'] = strip_tags($product->getData($value['magento_attribute_code']));
                    if (empty($item['description'])) {
                        $error = true;
                        $msg = "Description is missing";
                    }
                    break;
                case 'inventory':
                    $item['quantity'] = (int)$product->getData($value['magento_attribute_code']);
                    if (empty($item['quantity'])) {
                        $error = true;
                        $msg = "Dispatch Time Max is missing";
                    }
                    break;
                default:
                    break;
            }
            if ($error) {
                break;
            }
        }
        if ($error) {
            $item['type'] = "error";
            $item['data'] = $msg;
        }
        return $item;
    }
}
