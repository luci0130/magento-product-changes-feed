<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Model\Service;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;
use Turiac\SkuChange\Api\Data\ProductChangesInterface;
use Turiac\SkuChange\Api\ProductChangesRepositoryInterface;
use Turiac\SkuChange\Model\ProductChangesQueue\ProductChangesQueueInterface;

class SaveProductChanges implements SaveProductChangesInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ProductChangesRepositoryInterface
     */
    protected $productChangesRepository;

    /**
     * @var ProductChangesQueueInterface
     */
    private $productChangesQueue;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Product constructor.
     *
     * @param LoggerInterface $logger
     * @param ProductChangesQueueInterface $productChangesQueue
     * @param ProductChangesRepositoryInterface $productChangesRepository
     * @param RequestInterface $request
     */
    public function __construct(
        LoggerInterface $logger,
        ProductChangesQueueInterface $productChangesQueue,
        ProductChangesRepositoryInterface $productChangesRepository,
        RequestInterface $request
    ) {
        $this->logger = $logger;
        $this->productChangesQueue = $productChangesQueue;
        $this->productChangesRepository = $productChangesRepository;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function saveProductChanges(Product $product, array $fields): bool
    {
        try {
            $productChanges = $this->saveProductChangesInDb($product, $fields);
            if ($productChanges) {
                return $this->productChangesQueue->addToQueue($productChanges->getData()) ?? false;
            }
            return false;
        } catch (\Exception $e) {
            $this->logger->error('Error saving product changes on third party: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Saves the product changes to the database.
     *
     * @param Product $product
     * @param array $fields
     * @return false|ProductChangesInterface
     */
    private function saveProductChangesInDb(Product $product, array $fields)
    {
        try {
            $dataJson = json_encode($this->getChangedAttributes($product, $fields));
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('JSON encoding error: ' . json_last_error_msg());
                return null;
            }

            $productChanges = $this->productChangesRepository->create();
            $productChanges->setSku($product->getSku());
            $productChanges->setUpdatedData($dataJson);
            $productChanges->setStore($product->getStoreId());
            $productChanges->setSource($this->determineSource());
            $productChanges->setTime(date('Y-m-d H:i:s')); // Current time

            $this->productChangesRepository->save($productChanges);

            return $productChanges;
        } catch (\Exception $e) {
            $this->logger->error('Error saving product changes: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Returns an array of changed attributes for the product.
     *
     * @param Product $product
     * @param array $fields
     * @return array
     */
    protected function getChangedAttributes(Product $product, array $fields): array
    {
        $data = [];
        foreach ($fields as $key => $field) {
            if (is_numeric($key)) {
                $data[$field] = $product->getData($field);
            } else {
                $data[$key] = $field;
            }
        }

        return $data;
    }

    /**
     * Determines the source of the current request.
     *
     * @return string
     */
    private function determineSource(): string
    {
        $moduleName = $this->request->getModuleName();
        $controllerName = $this->request->getControllerName();
        $actionName = $this->request->getActionName();

        return "{$moduleName}_{$controllerName}_{$actionName}";
    }

}