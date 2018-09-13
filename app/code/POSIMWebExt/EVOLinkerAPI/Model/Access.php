<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace POSIMWebExt\EVOLinkerAPI\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use POSIMWebExt\EVOLinkerAPI\Api\AccessInterface;


/**
 * Defines the implementation class of the access service contract.
 */
class Access implements AccessInterface
{
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;
    protected $connection;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;
    /** @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface */
    protected $attributeRepository;
    /** @var ProductRepositoryInterface */
    protected $productRepoInterface;
    /** @var \Magento\Framework\Api\SearchCriteriaInterface */
    protected $searchCriteria;
    /** @var \POSIMWebExt\WebExtManager\Logger\Logger */
    protected $logger;
    /** @var \Magento\Catalog\Api\Data\ProductAttributeInterface */
    protected $productAttributeInterface;
    /** @var \Magento\Catalog\Model\ProductInterfaceFactory */
    protected $productInterfaceFactory;
    /** @var \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory */
    protected $stockItemInterfaceFactory;
    /** @var \Magento\Catalog\Api\ProductAttributeGroupRepositoryInterface */
    protected $productAttributeGroupRepository;
    /** @var \Magento\Framework\Api\Search\FilterGroup */
    protected $filterGroup;
    /**@var \Magento\Framework\Api\Filter */
    protected $filter;
    /** @var \Magento\Catalog\Api\ProductAttributeManagementInterface */
    protected $attributeManagement;
    /** @var \Magento\Catalog\Model\Product\Attribute\Repository */
    protected $productAttributeRepository;
    /** @var \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory */
    protected $optionInterface;
    /** @var \Magento\Catalog\Api\Data\ProductExtensionFactory */
    protected $productExtensionFactory;
    /** @var \Magento\ConfigurableProduct\Api\Data\OptionInterface */
    protected $configurableOptionsInterface;

    protected $confOptionValueInterfaceFactory;

    protected $test;

    protected $attributeInterfaceFactory;

    protected $productFactory;

    protected $productRepository;

    /**
     * Access constructor.
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory $productInterfaceFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $productAttributeInterface
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteriaInterface
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemInterfaceFactory
     * @param \Magento\Catalog\Api\ProductAttributeGroupRepositoryInterface $productAttributeGroupRepository
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\Filter $filter
     * @param \Magento\Catalog\Api\ProductAttributeManagementInterface $attributeManagement
     * @param \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionInterface
     * @param \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory
     * @param \Magento\ConfigurableProduct\Api\Data\OptionInterface $configOptionsInterface
     * @param \Magento\ConfigurableProduct\Api\Data\OptionValueInterfaceFactory $optionValueInterfaceFactory
     * @param \POSIMWebExt\WebExtManager\Logger\Logger $logger
     * @param array $data
     */

    public function __construct(
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productInterfaceFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Catalog\Api\Data\ProductAttributeInterfaceFactory $productAttributeInterface,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteriaInterface,
        \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemInterfaceFactory,
        \Magento\Catalog\Api\ProductAttributeGroupRepositoryInterface $productAttributeGroupRepository,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\Filter $filter,
        \Magento\Catalog\Api\ProductAttributeManagementInterface $attributeManagement,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionInterface,
        \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory,
        \Magento\ConfigurableProduct\Api\Data\OptionInterface $configOptionsInterface,
        \Magento\ConfigurableProduct\Api\Data\OptionValueInterfaceFactory $optionValueInterfaceFactory,
        \Magento\ConfigurableProduct\Helper\Product\Options\Factory $test,
        \Magento\Framework\Api\AttributeInterfaceFactory $attributeInterfaceFactory,
        \POSIMWebExt\WebExtManager\Logger\Logger $logger,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,

        array $data = []
    )
    {
        $this->productAttributeInterface = $productAttributeInterface;
        $this->_resource = $resource;
        $this->productInterfaceFactory = $productInterfaceFactory;
        $this->storeManager = $storeManager;
        $this->productRepoInterface = $productRepositoryInterface;
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteria = $searchCriteriaInterface;
        $this->stockItemInterfaceFactory = $stockItemInterfaceFactory;
        $this->productAttributeGroupRepository = $productAttributeGroupRepository;
        $this->filterGroup = $filterGroup;
        $this->filter = $filter;
        $this->attributeManagement = $attributeManagement;
        $this->optionInterface = $optionInterface;
        $this->productExtensionFactory = $productExtensionFactory;
        $this->configurableOptionsInterface = $configOptionsInterface;
        $this->confOptionValueInterfaceFactory = $optionValueInterfaceFactory;
        $this->test = $test;
        $this->logger = $logger;
        $this->attributeInterfaceFactory = $attributeInterfaceFactory;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;

    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_write');
        }
        return $this->connection;
    }

    /**
     * Return the string array of catalog SKUs.
     *
     * @api
     * @return string|array The string array of catalog SKUs.
     */
    public function getSkus()
    {
        $table = $this->_resource->getTableName('catalog_product_entity');
        $skus = $this->getConnection()->fetchAssoc('SELECT sku, attribute_set_id from `' . $table . '`');
        return $skus;
    }

    /**
     * Return the array of websites.
     *
     * @api
     * @return string|array The string array of websites
     */
    public function getWebsites()
    {
        $websiteArray = array();
        foreach ($this->storeManager->getWebsites() as $website) {
            $id = $website->getId();
            $code = $website->getCode();
            $name = $website->getName();
            $groupArray = array();
            foreach ($website->getGroups() as $group) {
                $groupId = $group->getId();
                $groupName = $group->getName();
                $groupArray[] = array(
                    'group_id' => $groupId,
                    'group_name' => $groupName,
                );
            }
            $currentSite = array(
                'website_id' => $id,
                'website_code' => $code,
                'website_name' => $name,
                'group_details' => $groupArray
            );
            $websiteArray[] = $currentSite;
        }
        return $websiteArray;
    }

    /**
     * Return the array of tax classes.
     *
     * @api
     * @return string|array The string array of tax classes
     */
    public function getTaxClasses()
    {
        $table = $this->_resource->getTableName('tax_class');
        $sql = 'SELECT class_id, class_name
		        FROM ' . $table . '
			    WHERE class_type = \'PRODUCT\'';
        return $this->getConnection()->fetchAll($sql);
    }

    /**
     * Create products via EVO integration
     *
     * @api
     * @param mixed array $product
     * @param bool $saveOptions
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function productsEvo($product, $saveOptions = false)
    {
        $websiteIds = array();
        $extensionAttributes = array();
        $customAttributes = array();

        $productDetails = $this->productInterfaceFactory->create($product);

        if (array_key_exists('extension_attributes', $product)):
            $this->logger->debug('######################################################');
            $this->logger->debug(100, $product['extension_attributes']);
            $extensionAttributes = $product['extension_attributes'];
            unset($product['extension_attributes']);
        endif;
        if (array_key_exists('website_ids', $product)):
            $websiteIds = $product['website_ids'];
            unset($product['website_ids']);
        endif;
        if (array_key_exists('custom_attributes', $product)):
           # $this->logger->debug('Custom attribute data:');
           # $this->logger->debug($product['custom_attributes']);
            $customAttributes = $product['custom_attributes'];
            unset($product['custom_attributes']);
        endif;

        $productDetails->fromArray($product);
        $this->logger->debug('*********************************************************');
        $this->logger->debug('Received data for '. $productDetails->getSku());
        if (count($websiteIds) > 0):
            $this->logger->debug('Received the following website IDs');
            $this->logger->debug(100, $websiteIds);
            $productDetails->setWebsiteIds($websiteIds);
        endif;

        if (count($customAttributes) > 0):
            $this->addNewAttributes($customAttributes, $product['attribute_set_id']);
            $this->checkAndAddValues($customAttributes);

            foreach ($customAttributes as &$custom) {
                if ($custom['attribute_code'] !== null && $custom['attribute_code'] !== 'category_ids'):
                    $custom[$custom['attribute_code']] = $custom['value'];
                    $this->logger->debug('Set '.$custom['attribute_code'].' to '.$custom['value']);
                    unset($custom['attribute_code'], $custom['value']);
                endif;
            }
            for($i=1, $iMax = count($customAttributes); $i<$iMax; $i++)
            {
                $customAttributes[0] = array_merge($customAttributes[$i], $customAttributes[0]);
                unset($customAttributes[$i]);
            }

            $productDetails->setCustomAttributes($customAttributes[0]);

        else:
            $this->logger->debug('No custom attributes were received.');
        endif;

        if (count($extensionAttributes) > 0):
            /** TODO check if catalogInventory is enabled */
            // if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            // }
            $extensionAttributesObj = $this->productExtensionFactory->create();

            if (array_key_exists('stock_item', $extensionAttributes) && $extensionAttributes['stock_item'] !== null):

                $stockItemObj = $this->stockItemInterfaceFactory->create();

                if(array_key_exists('qty', $extensionAttributes['stock_item'])):
                    $stockItemObj->setQty($extensionAttributes['stock_item']['qty']);
                    $this->logger->debug('set qty to '.$extensionAttributes['stock_item']['qty']);
                endif;

                if(array_key_exists('is_in_stock', $extensionAttributes['stock_item'])):
                    $stockItemObj->setIsInStock($extensionAttributes['stock_item']['is_in_stock']);
                    $this->logger->debug('set is_in_stock to '.$extensionAttributes['stock_item']['is_in_stock']);
                endif;

                if(array_key_exists('is_qty_decimal', $extensionAttributes['stock_item'])):
                    $stockItemObj->setIsQtyDecimal($extensionAttributes['stock_item']['is_qty_decimal']);
                    $this->logger->debug('set is_qty_decimal to '.$extensionAttributes['stock_item']['is_qty_decimal']);
                endif;

                if(array_key_exists('manage_stock', $extensionAttributes['stock_item'])):
                    $stockItemObj->setManageStock($extensionAttributes['stock_item']['manage_stock']);
                    $this->logger->debug('set manage_stock to '.$extensionAttributes['stock_item']['manage_stock']);
                endif;

                if(array_key_exists('use_config_manage_stock', $extensionAttributes['stock_item'])):
                    $stockItemObj->setUseConfigManageStock($extensionAttributes['stock_item']['use_config_manage_stock']);
                    $this->logger->debug('set use_config_manage_stock to '.$extensionAttributes['stock_item']['use_config_manage_stock']);
                endif;



                $extensionAttributesObj->setStockItem($stockItemObj);

            endif;

            if (array_key_exists('configurable_product_options', $extensionAttributes) && null !== $extensionAttributes['configurable_product_options']):
                $cpOptions = $extensionAttributes['configurable_product_options'];
                $this->logger->debug('Adding configurable options');
                foreach ($cpOptions as &$option) {

                    $this->logger->debug(100, $option);
                    $attribute = $this->attributeRepository->get($option['attribute_id']);
                    $attrId = $attribute->getAttributeId();
                    unset($attribute);

                    //get value indices instead of labels
                    $valueIndices = array();

                    foreach ($option['values'] as $value) {
                        $valueIndex = $this->checkAttributeValues($option['attribute_id'], $value['value_index']);
                        $valueIndices[] = array('value_index' => $valueIndex);
                    }
                    $option['attribute_id'] = $attrId;
                    $option['values'] = $valueIndices;

                }
                unset($option);
                $configurableOptions = $this->test->create($cpOptions);

                //trades skus for product ids
                $childrenSkus = $extensionAttributes['configurable_product_links'];
                $this->logger->debug('Attempting to add Associated Products:');
                $this->logger->debug(100, $extensionAttributes['configurable_product_links']);
                $childrenIds = array();
                foreach ($childrenSkus as $sku):
                    $product = $this->productRepoInterface->get($sku);
                    $childrenIds[] = $product->getId();
                endforeach;

                //setConfigurableOptions
                $extensionAttributesObj->setConfigurableProductOptions($configurableOptions);
                //setConfigurableProductLinks
                $extensionAttributesObj->setConfigurableProductLinks($childrenIds);

            endif;

            $productDetails->setExtensionAttributes($extensionAttributesObj);

        endif;


        return $this->productRepoInterface->save($productDetails, $saveOptions);


    }
    /* Todo: add batch products */

    /**
     * @param array $attributesData
     * @param $attributeSetId
     * @return string $frontendInput | null
     */
    private function addNewAttributes(array $attributesData, $attributeSetId)
    {
        $textInputs = array('mfgsku', 'upc', 'vendorsku', 'notes_warranty');
        $frontendInput = null;
        foreach ($attributesData as $customAttr) {
            if ($customAttr['attribute_code'] !== null):
                $codeName = preg_replace('/[^a-z0-9]+/', '_', strtolower(trim($customAttr['attribute_code'])));
                $codeValue = $customAttr['value'];
                try {
                    $attribute = $this->attributeRepository->get($codeName);
                    $frontendInput = $attribute->getFrontendInput();
                    try {
                        $setAttributes = $this->attributeManagement->getAttributes($attributeSetId);
                        $foundFlag = false;
                        foreach ($setAttributes as $attribute) {
                            if ($attribute->getAttributeCode() === $codeName):
                                $foundFlag = true;
                            endif;
                            if ($foundFlag):
                                break;
                            endif;
                        }
                        if (!$foundFlag):
                            $this->addAttributeToSet($attributeSetId, $codeName);
                        endif;
                    } catch (NoSuchEntityException $e) {
                        $this->logger->debug('Attribute set does not exist. Please sync attribute sets in EVO E-Commerce settings.');
                    }


                } catch (NoSuchEntityException $e) {
                    $frontendInput = 'select';

                    if (in_array($codeName, $textInputs, true)):
                        $frontendInput = 'text';
                        if ($codeName === 'notes_warranty'):
                            $frontendInput = 'textarea';
                        endif;
                    endif;
                    $this->logger->debug('Creating attribute ' . $codeName . ' with frontend input type ' . $frontendInput);

                    $productOption = $this->optionInterface->create();
                    $productOption->setLabel($customAttr['value']);

                    $attribute = $this->productAttributeInterface->create();

                    $attribute->setAttributeCode($codeName);
                    $attribute->setFrontendInput($frontendInput);
                    $attribute->setDefaultFrontendLabel($codeName);

                    if ($frontendInput === 'select'):
                        $attribute->setOptions(array($productOption));
                    endif;

                    $this->attributeRepository->save($attribute);

                    $this->addAttributeToSet($attributeSetId, $customAttr['attribute_code']);
                }
                if (null !== $frontendInput && $frontendInput === 'select'):
                    $this->checkAttributeValues($codeName, $codeValue);
                elseif
                ($this->attributeRepository->get($codeName)->getFrontendInput() === 'select'
                ):
                    $this->checkAttributeValues($codeName, $codeValue);
                endif;
            endif;
        }

    }

    /**
     * @param $attributeSetId
     * @param $attributeCode
     */
    private function addAttributeToSet($attributeSetId, $attributeCode)
    {
        $this->filter->setField('attribute_set_id');
        $this->filter->setValue($attributeSetId);
        $this->filter->setConditionType('eq');
        $this->filterGroup->setFilters(array($this->filter));

        $this->searchCriteria->setFilterGroups(array($this->filterGroup));
        $attributeGroups = $this->productAttributeGroupRepository->getList($this->searchCriteria);
        $groupIds = array();
        foreach ($attributeGroups->getItems() as $group) {
            $groupIds[$group->getAttributeGroupName()] = $group->getAttributeGroupId();
        }
        if (array_key_exists('Product Details', $groupIds)) {
            $groupId = $groupIds['Product Details'];
        } else {
            $groupId = array_pop($groupIds);
        }

        $this->attributeManagement->assign($attributeSetId, $groupId, $attributeCode, 99);


    }

    /**
     * @param $attributeCode
     * @param $value
     * @return string | null
     */

    private function checkAttributeValues($attributeCode, $value)
    {
        $textInputs = array('mfgsku', 'upc', 'vendorsku', 'notes_warranty');

        $attribute = $this->attributeRepository->get($attributeCode);
        if (!in_array($attributeCode, $textInputs)):
            $attributeOptions = $attribute->getOptions();
            foreach ($attributeOptions as $option) {
                if ($option->getLabel() === $value || ($attributeCode == 'tax_class_id' && $option->getValue() == $value)) {
                    $this->logger->debug('option value '.$value.' for attribute code '. $attributeCode.' exists, using it.');
                    return $option->getValue();
                }
            }
            //if we make it here we didn't find it, create it and return the value
            $this->logger->debug('Option value '.$value.' for attribute code '. $attributeCode.' does not exist, attempting to create it.');
            $productOption = $this->optionInterface->create();
            $productOption->setLabel($value);

            $attribute->setOptions(array($productOption));

            $this->attributeRepository->save($attribute);

            return $productOption->getValue();
        else:
            return false;
        endif;

    }

    /**
     * @param array $customAttributes
     *
     */

    private function checkAndAddValues(&$customAttributes)
    {
        foreach ($customAttributes as &$customAttribute) {
            if ($customAttribute['attribute_code'] !== null):
                $attribute = $this->attributeRepository->get($customAttribute['attribute_code']);
                $frontendInput = $attribute->getFrontendInput();
                if ($frontendInput === 'select'):
                    $valueID = $this->checkAttributeValues($customAttribute['attribute_code'], $customAttribute['value']);
                    if ($valueID !== null):
                        $customAttribute['value'] = (string)$valueID;
                    else:
                        $this->logger->debug("skipping " . $customAttribute['attribute_code'] . " value of " . $customAttribute['value'] . ", wasn't a value option at time of update.");
                    endif;
                endif;
            endif;
        }

    }

    /**
     * Create products via EVO integration
     *
     * @api
     * @param mixed array $product
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function batchesEvo($product)
    {
        $attributeCodes = array();
        foreach($product as $single):
            print_r($single);

            $attributeCodes[] = $single['custom_attributes'];
        endforeach;
        $paredDown = array();
        foreach ($attributeCodes as $set):
            foreach (array_column($set, 'attribute_code') as $acode):
                $paredDown[] = $acode;
                $paredDown = array_unique($paredDown);
            endforeach;
        endforeach;
        //print_r($product);

        /*$productDataArray = array();
        foreach ($product as $individual) {
            $productDataArray[] = $this->productsEvo($individual, false);
            unset($individual);
        }
        return $productDataArray;*/
    }
}