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

/**
 * Class Data
 * @package Ced\Etsy\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    public $_curl;
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
     * @var \Etsy\EtsyClientFactory
     */
    public $client;

    /**
     * @var \Etsy\EtsyApiFactory
     */
    public $etsyapi;

    /**
     * Data constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param Manager $manager
     * @param DirectoryList $directoryList
     * @param \Magento\Framework\Json\Helper\Data $json
     * @param Curl $curl
     * @param Session $session
     * @param \Etsy\EtsyClientFactory $client
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        Manager $manager,
        DirectoryList $directoryList,
        \Magento\Framework\Json\Helper\Data $json,
        Curl $curl,
        Session $session,
        \Etsy\EtsyClientFactory $client,
        \Etsy\EtsyApiFactory $apiFactory
    ) {
        $this->objectManager = $objectManager;
        $this->_resource = $curl;
        parent::__construct($context);
        $this->messageManager = $manager;
        $this->directoryList = $directoryList;
        $this->json = $json;
        $this->adminSession = $session;
        $this->consumerkey = $this->scopeConfig->getValue('etsy_config/etsy_setting/consumer_key');
        $this->consumersecretkey = $this->scopeConfig->getValue('etsy_config/etsy_setting/consumer_secret_key');
        $this->accesstoken = $this->scopeConfig->getValue('etsy_config/etsy_setting/access_token');
        $this->accesstokensecret = $this->scopeConfig->getValue('etsy_config/etsy_setting/access_token_secret');
        $this->client = $client;
        $this->etsyapi = $apiFactory;
    }

    /**
     * Check For Licence
     * @return boolean
     */
    public function checkForLicence()
    {
        if($this->_request->getModuleName() != 'etsy') {
            return $this;
        }
        $helper = $this->objectManager->create('Ced\Etsy\Helper\Feed');
        $modules = $helper->getCedCommerceExtensions();
        foreach ($modules as $moduleName=>$releaseVersion)
        {

            $m = strtolower($moduleName); if(!preg_match('/ced/i',$m)){ return $this; }

            $h = $this->scopeConfig->getValue(\Ced\Etsy\Block\Extensions::HASH_PATH_PREFIX.$m.'_hash');

            for($i=1;$i<=(int)$this->scopeConfig->getValue(\Ced\Etsy\Block\Extensions::HASH_PATH_PREFIX.$m.'_level');$i++)
            {
                $h = base64_decode($h);
            }

            $h = json_decode($h,true);
            if($moduleName == "Magento2_Ced_Etsy")
                if(is_array($h) && isset($h['domain']) && isset($h['module_name']) && isset($h['license']) && strtolower($h['module_name']) == $m && $h['license'] == $this->scopeConfig->getValue(\Ced\Etsy\Block\Extensions::HASH_PATH_PREFIX.$m)) {
                    return $this;
                }else{
                    return false;
                }
        }
        return $this;
    }

    /**
     * @param $path
     * @return array|bool|mixed|string
     */

    public function loadFile($path)
    {
        $data = [];
        if (file_exists($path)) {
            $pathInfo = pathinfo($path);
            if ($pathInfo['extension'] == 'json') {
                $myfile = fopen($path, "r");
                $data = fread($myfile, filesize($path));
                fclose($myfile);
                $data = $this->json->jsonDecode($data);
            }
        }
        return $data;
    }

    /**
     * @return \Etsy\EtsyApi
     */
    public function ApiObject()
    {
        $client = $this->client->create(
            [
                'consumer_key' => $this->consumerkey,
                'consumer_secret' => $this->consumersecretkey,
            ]
        );
        $client->authorize($this->accesstoken, $this->accesstokensecret);
        $api = $this->etsyapi->create(
            [
                'client' => $client
            ]
        );
        return $api;
    }
    /**
     * Function for getting Config value of current store
     *
     * @param string $path,
     */
    public function getStoreConfig($path,$storeId=null)
    {
        $this->_storeManager = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $store=$this->_storeManager->getStore($storeId);
        return $this->scopeConfig->getValue($path, 'store', $store->getCode());
    }
}
