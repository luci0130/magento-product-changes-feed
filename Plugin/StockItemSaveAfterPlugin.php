<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\IsProductSalableInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Turiac\SkuChange\Model\ProductChangesConfig;
use Turiac\SkuChange\Model\Service\SaveProductChangesInterface;

class StockItemSaveAfterPlugin
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SaveProductChangesInterface
     */
    private $saveProductChangesService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GetProductSalableQtyInterface
     */
    private $getProductSalableQty;

    /**
     * @var IsProductSalableInterface
     */
    private $isProductSalable;

    /**
     * @var StockResolverInterface
     */
    private $stockResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductChangesConfig
     */
    private $productChangesConfig;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SaveProductChangesInterface $saveProductChangesService
     * @param LoggerInterface $logger
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param IsProductSalableInterface $isProductSalable
     * @param ProductChangesConfig $productChangesConfig
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SaveProductChangesInterface $saveProductChangesService,
        LoggerInterface $logger,
        GetProductSalableQtyInterface $getProductSalableQty,
        IsProductSalableInterface $isProductSalable,
        ProductChangesConfig $productChangesConfig
    ) {
        $this->productRepository = $productRepository;
        $this->saveProductChangesService = $saveProductChangesService;
        $this->logger = $logger;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->isProductSalable = $isProductSalable;
        $this->productChangesConfig = $productChangesConfig;
    }

    /**
     * This plugin will catch also when a parent change his status to out of stock
     * vendor/magento/module-bundle/Model/Inventory/ChangeParentStockStatus.php:88
     *
     * @param StockItemRepository $subject
     * @param StockItemInterface $result
     * @return StockItemInterface
     */
    public function afterSave(StockItemRepository $subject, StockItemInterface $result)
    {
        if (!$this->productChangesConfig->isEnabled() || !$this->productChangesConfig->isStockTrackingEnabled()) {
            return $result;
        }

        try {
            $product = $this->productRepository->getById($result->getProductId());
            $stockId = $result->getStockId();
            $fields = $this->prepareFields($result, $product, $stockId);

            if ($this->saveProductChangesService->saveProductChanges($product, $fields)) {
                $this->logger->info("Stock changes successfully saved for SKU: {$product->getSku()}");
            } else {
                $this->logger->error("Failed to save stock changes for SKU: {$product->getSku()}");
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->warning("Product not found: {$e->getMessage()}");
        } catch (LocalizedException $e) {
            $this->logger->error("Stock Resolver error: {$e->getMessage()}");
        }

        return $result;
    }

    /**
     * @param StockItemInterface $stockItem
     * @param Product $product
     * @param int $stockId
     * @return array
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     */
    private function prepareFields($stockItem, $product, $stockId): array
    {
        return [
            'stock_qty'    => $stockItem->getQty(),
            'stock_status' => $stockItem->getIsInStock() ? 'In Stock' : 'Out of Stock',
            'is_salable'   => $this->isProductSalable->execute($product->getSku(), $stockId),
            'saleable_qty' => $this->getProductSalableQty->execute($product->getSku(), $stockId),
        ];
    }
}
