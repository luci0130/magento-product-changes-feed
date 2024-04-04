<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Api\Data;

interface ProductChangesInterface
{

    const SOURCE = 'source';
    const ATTRIBUTE = 'attribute';
    const PRODUCTCHANGES_ID = 'productchanges_id';
    const STORE = 'store';
    const TIME = 'time';
    const SKU = 'sku';
    const VALUE = 'value';

    /**
     * Get productchanges_id
     * @return string|null
     */
    public function getProductchangesId();

    /**
     * Set productchanges_id
     * @param string $productchangesId
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setProductchangesId($productchangesId);

    /**
     * Get sku
     * @return string|null
     */
    public function getSku();

    /**
     * Set sku
     * @param string $sku
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setSku($sku);

    /**
     * Get store
     * @return string|null
     */
    public function getStore();

    /**
     * Set store
     * @param string $store
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setStore($store);

    /**
     * Get source
     * @return string|null
     */
    public function getSource();

    /**
     * Set source
     * @param string $source
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setSource($source);

    /**
     * Get attribute
     * @return string|null
     */
    public function getAttribute();

    /**
     * Set attribute
     * @param string $attribute
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setAttribute($attribute);

    /**
     * Get value
     * @return string|null
     */
    public function getValue();

    /**
     * Set value
     * @param string $value
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setValue($value);

    /**
     * Get time
     * @return string|null
     */
    public function getTime();

    /**
     * Set time
     * @param string $time
     * @return \Turiac\SkuChange\ProductChanges\Api\Data\ProductChangesInterface
     */
    public function setTime($time);
}

