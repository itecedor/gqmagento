<?php

namespace POSIMWebExt\EVOLinkerAPI\Plugin\Product;

class RestSkuFix
{
    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param string $sku
     * @param bool $editMode
     * @param int|null $storeId
     * @param bool $forceReload
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return array
     */
    public function beforeGet($productRepository, $sku, $editMode = false, $storeId = null, $forceReload = false)
    {
        $sku = urldecode($sku);
        return [$sku, $editMode, $storeId, $forceReload];
    }

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param bool $saveOptions
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function beforeSave($productRepository, \Magento\Catalog\Api\Data\ProductInterface $product, $saveOptions = false)
    {
        $product->setSku(urldecode($product->getSku()));
    }

    /**
     * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     * @param string $productSku
     * @param int $scopeId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeGetStockItemBySku($stockRegistry, $productSku, $scopeId = null)
    {
        $productSku = urldecode($productSku);
        return [$productSku, $scopeId];
    }

    /**
     * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     * @param string $productSku
     * @param int $scopeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return array
     */
    public function beforeGetStockStatusBySku($stockRegistry, $productSku, $scopeId = null)
    {
        $productSku = urldecode($productSku);
        return [$productSku, $scopeId];
    }

    /**
     * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     * @param string $productSku
     * @param int $scopeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return array
     */
    public function beforeGetProductStockStatusBySku($stockRegistry, $productSku, $scopeId = null)
    {
        $productSku = urldecode($productSku);
        return [$productSku, $scopeId];
    }

    /**
     * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     * @param string $productSku
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return array
     */
    public function beforeUpdateStockItemBySku($stockRegistry, $productSku, \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem)
    {
        $productSku = urldecode($productSku);
        return [$productSku, $stockItem];
    }
}