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
* @url         https://www.milople.com/magento2-extension/request-for-quote-m2.html
*
**/
namespace Milople\Requestforquote\Setup;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
class UpgradeData implements UpgradeDataInterface
{
/**
* EAV setup factory
*
* @var EavSetupFactory
*/
private $eavSetupFactory;
/**
* Init
*
* @param EavSetupFactory $eavSetupFactory
*/
public function __construct(EavSetupFactory $eavSetupFactory)
{
	$this->eavSetupFactory = $eavSetupFactory;
}
	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		/** @var EavSetup $eavSetup */
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
		/**
		* Add attributes to the eav/attribute
		*/
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Product::ENTITY,
			'enable_requestforquote',
			[
			'group' => 'Request For Quote',
			'type' => 'int',
			'backend' => '',
			'frontend' => '',
			'label' => 'Enable Request For Quote',
			'input' => 'select',
			'class' => '',
			'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
			'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'visible' => true,
			'required' => false,
			'user_defined' => true,
			'default' => false,
			'searchable' => false,
			'filterable' => false,
			'comparable' => false,
			'visible_on_front' => false,
			'used_in_product_listing' => true,
			'unique' => false,
			'apply_to' => 'simple,configurable,virtual,bundle,downloadable,grouped'
			]
			); 
	}
}