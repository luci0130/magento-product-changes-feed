<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Model\ResourceModel\ProductChanges;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'productchanges_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Turiac\SkuChange\Model\ProductChanges::class,
            \Turiac\SkuChange\Model\ResourceModel\ProductChanges::class
        );
    }
}

