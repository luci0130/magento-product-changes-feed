<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Observer\Catalog;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product as CatalogProduct;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Turiac\SkuChange\Model\ProductChangesQueue\ProductChangesQueueInterface;

class ProductSaveAfter implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var ProductChangesQueueInterface
     */
    private $productChangesQueue;


    /**
     * Product constructor.
     *
     * @param LoggerInterface $logger
     * @param ProductChangesQueueInterface $productChangesQueue
     * @param array $fields
     */
    public function __construct(
        LoggerInterface $logger,
        ProductChangesQueueInterface $productChangesQueue,
        array $fields
    ) {
        $this->logger = $logger;
        $this->fields = $fields;
        $this->productChangesQueue = $productChangesQueue;
    }


    public function execute(Observer $observer): void
    {
        /** @var Product $product */
        $product = $observer->getEvent()->getProduct();

        if ($product instanceof CatalogProduct) {
            foreach ($this->fields as $field) {
                if (!$product->dataHasChangedFor($field)) {
                    continue;
                }
                $this->queueProductChangeInformation($product, $field);
            }
        }
    }

    private function queueProductChangeInformation(Product $product, $field): void
    {
        $data = [
            'sku'       => $product->getSku(),
            'field'     => $field,
            'newValue'  => $product->getData($field),
            'store_id'  => $product->getStoreId(), // Include the store ID
            'timestamp' => time(),
        ];

        $this->productChangesQueue->addToQueue($data);
    }
}


