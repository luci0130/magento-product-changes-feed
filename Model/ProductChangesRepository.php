<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Turiac\SkuChange\Api\Data\ProductChangesInterface;
use Turiac\SkuChange\Api\Data\ProductChangesInterfaceFactory;
use Turiac\SkuChange\Api\Data\ProductChangesSearchResultsInterfaceFactory;
use Turiac\SkuChange\Api\ProductChangesRepositoryInterface;
use Turiac\SkuChange\Model\ResourceModel\ProductChanges as ResourceProductChanges;
use Turiac\SkuChange\Model\ResourceModel\ProductChanges\CollectionFactory as ProductChangesCollectionFactory;

class ProductChangesRepository implements ProductChangesRepositoryInterface
{

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var ProductChangesCollectionFactory
     */
    protected $productChangesCollectionFactory;

    /**
     * @var ResourceProductChanges
     */
    protected $resource;

    /**
     * @var ProductChangesInterfaceFactory
     */
    protected $productChangesFactory;

    /**
     * @var ProductChanges
     */
    protected $searchResultsFactory;


    /**
     * @param ResourceProductChanges $resource
     * @param ProductChangesInterfaceFactory $productChangesFactory
     * @param ProductChangesCollectionFactory $productChangesCollectionFactory
     * @param ProductChangesSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceProductChanges $resource,
        ProductChangesInterfaceFactory $productChangesFactory,
        ProductChangesCollectionFactory $productChangesCollectionFactory,
        ProductChangesSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->productChangesFactory = $productChangesFactory;
        $this->productChangesCollectionFactory = $productChangesCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(ProductChangesInterface $productChanges)
    {
        try {
            $this->resource->save($productChanges);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the productChanges: %1',
                $exception->getMessage()
            ));
        }
        return $productChanges;
    }

    /**
     * @inheritDoc
     */
    public function get($productChangesId)
    {
        $productChanges = $this->productChangesFactory->create();
        $this->resource->load($productChanges, $productChangesId);
        if (!$productChanges->getId()) {
            throw new NoSuchEntityException(__('ProductChanges with id "%1" does not exist.', $productChangesId));
        }
        return $productChanges;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->productChangesCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(ProductChangesInterface $productChanges)
    {
        try {
            $productChangesModel = $this->productChangesFactory->create();
            $this->resource->load($productChangesModel, $productChanges->getProductchangesId());
            $this->resource->delete($productChangesModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ProductChanges: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($productChangesId)
    {
        return $this->delete($this->get($productChangesId));
    }
}

