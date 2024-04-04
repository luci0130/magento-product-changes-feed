<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Api\Data;

interface ProductChangesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get ProductChanges list.
     * @return \Turiac\SkuChange\Api\Data\ProductChangesInterface[]
     */
    public function getItems();

    /**
     * Set sku list.
     * @param \Turiac\SkuChange\Api\Data\ProductChangesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

