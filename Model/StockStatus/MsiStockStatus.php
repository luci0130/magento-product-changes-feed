<?php

namespace Turiac\SkuChange\Model\StockStatus;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\InventorySalesApi\Api\IsProductSalableInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

class MsiStockStatus implements StockStatusInterface
{
    /**
     * @var IsProductSalableInterface
     */
    private IsProductSalableInterface $isProductSalableInterface;

    /**
     * @var GetSalableQuantityDataBySku
     */
    private GetSalableQuantityDataBySku $getSalableQuantityDataBySku;

    /**
     * @var GetSourceItemsBySkuInterface
     */
    private GetSourceItemsBySkuInterface $getSourceItemsBySkuInterface;

    /**
     * @var StockResolverInterface
     */
    private StockResolverInterface $stockResolver;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    public function __construct(
        IsProductSalableInterface $isProductSalableInterface,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku,
        GetSourceItemsBySkuInterface $getSourceItemsBySkuInterface,
        StockResolverInterface $stockResolver,
        StoreManagerInterface $storeManager // Adjusted to use StoreManagerInterface
    )
    {
        $this->isProductSalableInterface = $isProductSalableInterface;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
        $this->getSourceItemsBySkuInterface = $getSourceItemsBySkuInterface;
        $this->stockResolver = $stockResolver;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function getStockStatus($sku)
    {
        try {
            $stockId = $this->getStockIdForCurrentWebsite();
            return $this->isProductSalableInterface->execute($sku, $stockId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function getStockQty($sku)
    {
        $qty = 0;
        try {
            $sourceItems = $this->getSourceItemsBySkuInterface->execute($sku);
            foreach ($sourceItems as $sourceItem) {
                if ($sourceItem->getStatus() == SourceItemInterface::STATUS_IN_STOCK) {
                    $qty += $sourceItem->getQuantity();
                }
            }
        } catch (\Exception $e) {
            return 0;
        }
        return $qty;
    }

    /**
     * @inheritDoc
     */
    public function isProductSaleable($sku)
    {
        try {
            $stockId = $this->getStockIdForCurrentWebsite();
            return $this->isProductSalableInterface->execute($sku, $stockId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function getSaleableQty($sku)
    {
        $salableQty = 0;
        try {
            $salableQuantities = $this->getSalableQuantityDataBySku->execute($sku);
            foreach ($salableQuantities as $salableQuantity) {
                $salableQty += $salableQuantity['qty'];
            }
        } catch (\Exception $e) {
            return 0;
        }
        return $salableQty;
    }

    protected function getStockIdForCurrentWebsite()
    {
        try {
            $websiteCode = $this->storeManager->getStore()->getWebsite()->getCode();
            return $this->stockResolver->execute('website', $websiteCode)->getStockId();
        } catch (NoSuchEntityException $e) {
            return 0;
        }
    }

}