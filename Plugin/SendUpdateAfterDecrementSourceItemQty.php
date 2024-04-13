<?php

namespace Turiac\SkuChange\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Inventory\Model\SourceItem\Command\DecrementSourceItemQty;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\IsProductSalableInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Turiac\SkuChange\Model\ProductChangesConfig;
use Turiac\SkuChange\Model\Service\SaveProductChangesInterface;

class SendUpdateAfterDecrementSourceItemQty
{
    private $saveProductChangesService;
    private $logger;
    private $productRepository;
    private $storeManager;
    private $stockResolver;
    private $isProductSalable;
    private $getProductSalableQty;
    private $productChangesConfig;

    public function __construct(
        SaveProductChangesInterface $saveProductChangesService,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        StockResolverInterface $stockResolver,
        IsProductSalableInterface $isProductSalable,
        GetProductSalableQtyInterface $getProductSalableQty,
        ProductChangesConfig $productChangesConfig
    ) {
        $this->saveProductChangesService = $saveProductChangesService;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->stockResolver = $stockResolver;
        $this->isProductSalable = $isProductSalable;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->productChangesConfig = $productChangesConfig;
    }

    /**
     * Update parent products stock status after decrementing quantity of children stock
     *
     * @param DecrementSourceItemQty $subject
     * @param void $result
     * @param SourceItemInterface[] $sourceItemDecrementData
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(
        DecrementSourceItemQty $subject,
        $result,
        array $sourceItemDecrementData
    ) {
        if (!$this->productChangesConfig->isEnabled() || !$this->productChangesConfig->isStockTrackingEnabled()) {
            return;
        }

        $sourceItems = array_column($sourceItemDecrementData, 'source_item');

        foreach ($sourceItems as $sourceItem) {
            try {
                $product = $this->productRepository->get($sourceItem->getSku());
                $fields = $this->prepareStockFields($product, $sourceItem);

                if (!$this->saveProductChangesService->saveProductChanges($product, $fields)) {
                    $this->logger->error("Failed to send stock update for SKU: {$sourceItem->getSku()}");
                }
            } catch (NoSuchEntityException $e) {
                $this->logger->warning("Product with SKU {$sourceItem->getSku()} not found: {$e->getMessage()}");
            } catch (\Exception $e) {
                $this->logger->error("Error sending update after decrementing source item qty: {$e->getMessage()}");
            }
        }
    }

    private function getStockId(): int
    {
        $websiteCode = $this->storeManager->getWebsite()->getCode();
        $stock = $this->stockResolver->execute('website', $websiteCode);
        return $stock->getStockId();
    }

    private function prepareStockFields($product, $sourceItem): array
    {
        $isInStock = $sourceItem->getStatus() == SourceItemInterface::STATUS_IN_STOCK;
        $sku = $product->getSku();

        $stockId = $this->getStockId();
        $isSalable = $this->isProductSalable->execute($sku, $stockId);
        $saleableQty = $this->getProductSalableQty->execute($sku, $stockId);

        return [
            'stock_qty'    => $sourceItem->getQuantity(),
            'stock_status' => $isInStock ? 'In Stock' : 'Out of Stock',
            'is_salable'   => $isSalable,
            'saleable_qty' => $saleableQty,
        ];
    }
}