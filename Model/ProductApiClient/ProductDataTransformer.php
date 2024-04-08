<?php

namespace Turiac\SkuChange\Model\ProductApiClient;

class ProductDataTransformer implements DataTransformerInterface
{
    public function transform(array $data): array
    {
        if(isset($data['updatedData'])){
            $data['updatedData'] = json_decode($data['updatedData'], true);
        }
        return $data;
    }
}