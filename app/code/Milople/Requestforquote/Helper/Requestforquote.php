<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Requestforquote
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extension/call-for-price-m2.html
*
**/
namespace Milople\Requestforquote\Helper;
class Requestforquote extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
	protected $applyCallForPrice;
    public function __construct(
	\Magento\Framework\App\RequestInterface $httpRequest, 
	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
	\Psr\Log\LoggerInterface $logger, 
	\Magento\Store\Model\StoreManagerInterface $storeManager,
	\Magento\Customer\Model\Session $session,
	\Magento\Catalog\Model\ProductFactory $_productloader,
	\Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
	
	)
    {
        $this -> scopeConfig = $scopeConfig;
        $this -> storeManager = $storeManager;
        $this -> logger = $logger;
        $this -> request = $httpRequest;
				$this -> session = $session;
				$this->_countryCollectionFactory = $countryCollectionFactory;
				$this->_productloader = $_productloader;

    }      
	public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	public function getCustomerGroups()
	{
		if (empty($this->applyCallForPrice))
		{
	       	$this->applyCallForPrice = $this->getConfig('requestforquote/license_status_group/requestforquote_customergroups');	
		}
		return explode(',',$this->applyCallForPrice);
	}	
	public function isValidCustomer($customerGroupId = NULL)
	{
		$customer_groups = $this->getCustomerGroups();
		if($customerGroupId == NULL)
		{
			$_loggedIn = $this->session->isLoggedIn();		
			if(!$_loggedIn)
			{
				$customerGroupId = 0;
			}
			else
			{
				$customerGroupId =$this->session->getCustomer()->getGroupId();
			}
		}
		if(in_array($customerGroupId,$customer_groups))
		{
			return true;
		}
		return false;
	}	
	public function getCountryCollection()
    {
        $collection = $this->_countryCollectionFactory->create()->loadByStore();
        return $collection;
    }
	 /**
         * Retrieve list of countries in array option
         *
         * @return array
         */
        public function getCountries()
        {
            return $options = $this->getCountryCollection()
                    ->setForegroundCountries($this->getTopDestinations())
                        ->toOptionArray();
        }
		/**
         * Retrieve list of top destinations countries
         *
         * @return array
         */
        protected function getTopDestinations()
        {
            $destinations = (string)$this->scopeConfig->getValue(
                'general/country/destinations',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            return !empty($destinations) ? explode(',', $destinations) : [];
        }
	
		public function isProductEnabledForrequestforquote($id){
			return $this->_productloader->create()->load($id)->getAttributeText('enable_requestforquote');	
		}
		public function getMediaURL()
		{
			return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)."upload/";
		}
}