<?php

namespace Turiac\SkuChange\Model\StockStatus;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class StockStatus implements StockStatusInterface
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Constructor
     *
     * @param StockRegistryInterface $stockRegistry
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        ProductRepositoryInterface $productRepository
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->productRepository = $productRepository;
    }

    public function getStockStatus($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
            $stockStatus = $this->stockRegistry->getStockStatus($product->getId());
            return (bool) $stockStatus->getStockStatus();
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    public function getStockQty($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
            $stockItem = $this->stockRegistry->getStockItem($product->getId());
            return $stockItem->getQty();
        } catch (NoSuchEntityException $e) {
            return 0;
        }
    }

    public function isProductSaleable($sku)
    {
        return $this->getStockStatus($sku);
    }

    public function getSaleableQty($sku)
    {
        return $this->getStockQty($sku);
    }
}