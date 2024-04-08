<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Api;

interface ProductChangesRepositoryInterface
{

    /**
     * Save ProductChanges
     *
     * @param \Turiac\SkuChange\Api\Data\ProductChangesInterface $productChanges
     * @return \Turiac\SkuChange\Api\Data\ProductChangesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Turiac\SkuChange\Api\Data\ProductChangesInterface $productChanges
    );

    /**
     * Create new ProductChanges entity
     *
     * @return \Turiac\SkuChange\Api\Data\ProductChangesInterface
     */
    public function create();

    /**
     * Retrieve ProductChanges
     *
     * @param string $productchangesId
     * @return \Turiac\SkuChange\Api\Data\ProductChangesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($productchangesId);

    /**
     * Retrieve ProductChanges matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Turiac\SkuChange\Api\Data\ProductChangesSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ProductChanges
     *
     * @param \Turiac\SkuChange\Api\Data\ProductChangesInterface $productChanges
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Turiac\SkuChange\Api\Data\ProductChangesInterface $productChanges
    );

    /**
     * Delete ProductChanges by ID
     *
     * @param string $productchangesId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($productchangesId);
}

