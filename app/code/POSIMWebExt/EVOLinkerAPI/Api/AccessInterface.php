<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace POSIMWebExt\EVOLinkerAPI\Api;

/**
 * Custom methods for EVOLinkerAPI
 */
interface AccessInterface
{
    /**
     * Return the array of SKUs in the catalog.
     *
     * @api
     * @return string|array The string array of catalog SKUs.
     */
    public function getSkus();

    /**
     * Return the array of websites in the catalog.
     *
     * @api
     * @return string|array The string array of websites
     */
    public function getWebsites();

    /**
     * Return the array of tax classes.
     *
     * @api
     * @return string|array The string array of tax classes
     */
    public function getTaxClasses();

    /**
     * Create products via EVO integration
     *
     * @api
     * @param mixed array $product
     * @param bool $saveOptions
     * @return \Magento\Catalog\Api\Data\ProductInterface $createdProduct
     */
    public function productsEvo($product, $saveOptions = false);

    /**
     * Create products via EVO integration
     *
     * @api
     * @param mixed array $product
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function batchesEvo($product);
}
