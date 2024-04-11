<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Observer\Catalog;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product as CatalogProduct;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Turiac\SkuChange\Model\ProductChangesConfig;
use Turiac\SkuChange\Model\Service\SaveProductChangesInterface;
use Turiac\SkuChange\Model\StockStatus\StockStatusFactory;
use Turiac\SkuChange\Model\StockStatus\StockStatusInterface;

class ProductSaveAfter implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SaveProductChangesInterface
     */
    private $productSaveChangesService;

    /**
     * @var ProductChangesConfig
     */
    private $productChangesConfig;

    /**
     * @var StockStatusFactory
     */
    private $stockStatusFactory;

    /**
     * Product constructor.
     *
     * @param LoggerInterface $logger
     * @param SaveProductChangesInterface $productSaveChangesService
     * @param ProductChangesConfig $productChangesConfig
     * @param StockStatusFactory $stockStatusFactory
     */
    public function __construct(
        LoggerInterface $logger,
        SaveProductChangesInterface $productSaveChangesService,
        ProductChangesConfig $productChangesConfig,
        StockStatusFactory $stockStatusFactory
    ) {
        $this->logger = $logger;
        $this->productChangesConfig = $productChangesConfig;
        $this->productSaveChangesService = $productSaveChangesService;
        $this->stockStatusFactory = $stockStatusFactory;
    }

    public function execute(Observer $observer): void
    {
        try {
            if (!$this->productChangesConfig->isEnabled()) {
                return;
            }

            /** @var Product $product */
            $product = $observer->getEvent()->getProduct();

            $allowedFields = $this->productChangesConfig->getFields();
            $fields = [];

            if ($product instanceof CatalogProduct) {
                foreach ($allowedFields as $field) {
                    if ($product->dataHasChangedFor($field)) {
                        $fields[] = $field;
                    }
                }

                if ($this->productChangesConfig->isStockTrackingEnabled()) {

                    if ($product->getExtensionAttributes()) {
                        $stockItem = $product->getExtensionAttributes()->getStockItem();
                        if ($stockItem) {
                            $fields['stock_qty'] = $stockItem->getQty();
                            $fields['stock_status'] = $stockItem->getIsInStock() ? 'In Stock' : 'Out of Stock';
                            $fields['is_saleable'] = $stockItem->getIsInStock();
                            $fields['saleable_qty'] = $stockItem->getQty();
                        } else {
                            /** @var StockStatusInterface $stockStatusModel */
                            $stockStatusModel = $this->stockStatusFactory->create();
                            $sku = $product->getSku();
                            $fields['stock_qty'] = $stockStatusModel->getStockQty($sku);
                            $fields['stock_status'] = $stockStatusModel->getStockStatus($sku) ? 'In Stock' : 'Out of Stock';
                            $fields['saleable_qty'] = $stockStatusModel->getSaleableQty($sku);
                            $fields['is_saleable'] = $stockStatusModel->isProductSaleable($sku);
                        }
                    }
                }

                if (!empty($fields)) {
                    $this->productSaveChangesService->saveProductChanges($product, $fields);
                }
            }
        } catch (\Throwable $exception) {
            $this->logger->error('Error processing product save after event: ' . $exception->getMessage());
        }
    }
}


