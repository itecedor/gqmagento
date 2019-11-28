<?php

namespace POSIMWebExt\GCLink\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use POSIMWebExt\GCLink\Model\Product\Type\Giftcard;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'posimgc_type',
            [
                'group'                   => 'Product Details',
                'type'                    => 'int',
                'backend'                 => '',
                'frontend'                => '',
                'sort_order'              => 1,
                'label'                   => 'Gift Card Type',
                'input'                   => 'select',
                'class'                   => '',
                'source'                  => '\Magento\Eav\Model\Entity\Attribute\Source\Table',
                'global'                  => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible'                 => true,
                'required'                => true,
                'user_defined'            => false,
                'default'                 => '',
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => false,
                'used_in_product_listing' => false,
                'unique'                  => false,
                'apply_to'                => 'posimgc',
                'option'                  =>
                    array(
                        'values' =>
                            array(
                                1 => 'Both Physical and Virtual',
                                2 => 'Virtual (email)',
                                3 => 'Physical (snail mail)',
                            ),
                    ),
            ]
        );
        $attributes = [
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'minimal_price',
            'tax_class_id'
        ];
        foreach ($attributes as $attributeCode) {
            $relatedProductTypes = explode(
                ',',
                $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode, 'apply_to')
            );
            if (!in_array(Giftcard::TYPE_GIFTCARD, $relatedProductTypes)) {
                $relatedProductTypes[] = Giftcard::TYPE_GIFTCARD;
                $eavSetup->updateAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attributeCode,
                    'apply_to',
                    join(',', $relatedProductTypes)
                );
            }
        }
    }
}