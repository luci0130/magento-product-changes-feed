<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Model;

use Magento\Framework\Model\AbstractModel;
use Turiac\SkuChange\Api\Data\ProductChangesInterface;

class ProductChanges extends AbstractModel implements ProductChangesInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Turiac\SkuChange\Model\ResourceModel\ProductChanges::class);
    }

    /**
     * @inheritDoc
     */
    public function getProductchangesId()
    {
        return $this->getData(self::PRODUCTCHANGES_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductchangesId($productchangesId)
    {
        return $this->setData(self::PRODUCTCHANGES_ID, $productchangesId);
    }

    /**
     * @inheritDoc
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * @inheritDoc
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @inheritDoc
     */
    public function getStore()
    {
        return $this->getData(self::STORE);
    }

    /**
     * @inheritDoc
     */
    public function setStore($store)
    {
        return $this->setData(self::STORE, $store);
    }

    /**
     * @inheritDoc
     */
    public function getSource()
    {
        return $this->getData(self::SOURCE);
    }

    /**
     * @inheritDoc
     */
    public function setSource($source)
    {
        return $this->setData(self::SOURCE, $source);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedData()
    {
        return $this->getData(self::UPDATEDDATA);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedData($updatedData)
    {
        return $this->setData(self::UPDATEDDATA, $updatedData);
    }

    /**
     * @inheritDoc
     */
    public function getTime()
    {
        return $this->getData(self::TIME);
    }

    /**
     * @inheritDoc
     */
    public function setTime($time)
    {
        return $this->setData(self::TIME, $time);
    }
}