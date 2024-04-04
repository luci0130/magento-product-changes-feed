<?php

namespace Turiac\SkuChange\Model\ProductApiClient;

class ProductDataTransformer implements DataTransformerInterface
{
    public function transform(array $data): array
    {
        // Implement the transformation logic here
        // This is a basic example that just passes data through
        // You would replace this with actual transformation logic
        return $data;
    }
}