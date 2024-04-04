<?php

namespace Turiac\SkuChange\Model\ProductApiClient;

interface DataTransformerInterface
{
    /**
     * Transforms product data into the desired format.
     *
     * @param array $data The original product data.
     * @return array The transformed data.
     */
    public function transform(array $data): array;
}