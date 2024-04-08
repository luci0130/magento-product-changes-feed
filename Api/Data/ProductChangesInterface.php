<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Api\Data;

interface ProductChangesInterface
{
    const PRODUCTCHANGES_ID = 'productchanges_id';
    const SKU               = 'sku';
    const STORE             = 'store';
    const UPDATEDDATA       = 'updatedData';
    const SOURCE            = 'source';
    const TIME              = 'time';

    /**
     * Get productchanges_id
     *
     * @return string|null
     */
    public function getProductchangesId();

    /**
     * Set productchanges_id
     *
     * @param string $productchangesId
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setProductchangesId($productchangesId);

    /**
     * Get sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Set sku
     *
     * @param string $sku
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setSku($sku);

    /**
     * Get store
     *
     * @return string|null
     */
    public function getStore();

    /**
     * Set store
     *
     * @param string $store
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setStore($store);

    /**
     * Get source
     *
     * @return string|null
     */
    public function getSource();

    /**
     * Set source
     *
     * @param string $source
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setSource($source);

    /**
     * Get updatedData
     *
     * @return string|null
     */
    public function getUpdatedData();

    /**
     * Set updatedData
     *
     * @param string $updatedData
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setUpdatedData($updatedData);

    /**
     * Get time
     *
     * @return string|null
     */
    public function getTime();

    /**
     * Set time
     *
     * @param string $time
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setTime($time);
}