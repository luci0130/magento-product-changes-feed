<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Model\Service;

use Magento\Catalog\Model\Product;

interface SaveProductChangesInterface
{
    /**
     * Save the changes made to a product on database and on third party
     *
     * @param Product $product The product entity that has changes.
     * @param array $fields Associative array of fields that have been changed.
     * @return bool Returns true on successful save operation, false on failure.
     */
    public function saveProductChanges(Product $product, array $fields): bool;
}