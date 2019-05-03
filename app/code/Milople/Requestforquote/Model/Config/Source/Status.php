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
* @url         https://www.milople.com/magento2-extensions/call-for-price-m2.html
*
**/
namespace Milople\Requestforquote\Model\Config\Source;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *  This function Work as a Source Model for "Status" field in size chart configuration.
     **/
    public function toOptionArray() {
      return [
              ['value' => 'all_products', 'label' => __('Enable for All Products')], 
              ['value' => 'specific_products', 'label' => __('Enable for Specific Products')],
              ['value' => 'disable', 'label' => __('Disable')]
      ];
    }
}
