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
     * Product constructor.
     *
     * @param LoggerInterface $logger
     * @param SaveProductChangesInterface $productSaveChangesService
     * @param ProductChangesConfig $productChangesConfig
     */
    public function __construct(
        LoggerInterface $logger,
        SaveProductChangesInterface $productSaveChangesService,
        ProductChangesConfig $productChangesConfig
    ) {
        $this->logger = $logger;
        $this->productChangesConfig = $productChangesConfig;
        $this->productSaveChangesService = $productSaveChangesService;
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

                if (!empty($fields)) {
                    $this->productSaveChangesService->saveProductChanges($product, $fields);
                }
            }
        } catch (\Throwable $exception) {
            $this->logger->error('Error processing product save after event: ' . $exception->getMessage());
        }
    }
}


