<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Observer\Stock;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\IsProductSalableInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Turiac\SkuChange\Model\ProductChangesConfig;
use Turiac\SkuChange\Model\Service\SaveProductChangesInterface;

class StockItemSaveAfter implements ObserverInterface
{
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
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductChangesConfig
     */
    private $productChangesConfig;

    /**
     * @param SaveProductChangesInterface $saveProductChangesService
     * @param LoggerInterface $logger
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param IsProductSalableInterface $isProductSalable
     * @param StockResolverInterface $stockResolver
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param ProductChangesConfig $productChangesConfig
     */
    public function __construct(
        SaveProductChangesInterface $saveProductChangesService,
        LoggerInterface $logger,
        GetProductSalableQtyInterface $getProductSalableQty,
        IsProductSalableInterface $isProductSalable,
        StockResolverInterface $stockResolver,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        ProductChangesConfig $productChangesConfig
    ) {
        $this->saveProductChangesService = $saveProductChangesService;
        $this->logger = $logger;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->isProductSalable = $isProductSalable;
        $this->stockResolver = $stockResolver;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->productChangesConfig = $productChangesConfig;
    }

    /**
     * @throws LocalizedException
     * @throws InputException
     */
    public function execute(Observer $observer)
    {
        if (!$this->productChangesConfig->isEnabled() || !$this->productChangesConfig->isStockTrackingEnabled()) {
            return;
        }

        try {
            $stockItem = $observer->getEvent()->getData('item');
            $product = $this->productRepository->getById($stockItem->getProductId());
            $sku = $product->getSku();

            $stockId = $this->getStockIdForCurrentWebsite();
            $fields = $this->prepareStockFields($sku, $stockId, $stockItem);

            if (!$this->saveProductChangesService->saveProductChanges($product, $fields)) {
                throw new LocalizedException(__("Failed to save stock changes for SKU: {$sku}"));
            }

            $this->logger->info("Stock changes successfully saved and dispatched for SKU: {$sku}");
        } catch (NoSuchEntityException $e) {
            $this->logger->warning("Product or stock not found: {$e->getMessage()}");
        } catch (LocalizedException $e) {
            $this->logger->critical($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error("Unexpected error: {$e->getMessage()}");
        }
    }

    /**
     * Prepare stock fields for saving.
     *
     * @param string $sku
     * @param int $stockId
     * @param $stockItem
     * @return array
     */
    private function prepareStockFields($sku, $stockId, $stockItem): array
    {
        $isSalable = $this->isProductSalable->execute($sku, $stockId);
        try {
            $saleableQty = $this->getProductSalableQty->execute($sku, $stockId);
        } catch (InputException $e) {
        } catch (LocalizedException $e) {
        }

        return [
            'stock_qty'    => $stockItem->getQty(),
            'stock_status' => $stockItem->getIsInStock() ? 'In Stock' : 'Out of Stock',
            'is_salable'   => $isSalable,
            'saleable_qty' => $saleableQty ?? $stockItem->getQty(),
        ];
    }

    /**
     * Get the stock ID for the current website.
     *
     * @return int
     * @throws NoSuchEntityException|LocalizedException
     */
    private function getStockIdForCurrentWebsite(): int
    {
        $websiteCode = $this->storeManager->getWebsite()->getCode();
        return $this->stockResolver->execute('website', $websiteCode)->getStockId();
    }
}
