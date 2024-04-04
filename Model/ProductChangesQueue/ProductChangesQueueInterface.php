<?php

namespace Turiac\SkuChange\Model\ProductChangesQueue;

interface ProductChangesQueueInterface
{
    /**
     * Adds data to the queue.
     *
     * @param array $data Data to add to the queue.
     * @return bool True on success, false on failure.
     */
    public function addToQueue(array $data): bool;
}