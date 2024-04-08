<?php

namespace Turiac\SkuChange\Model\ProductChangesQueue;

use Turiac\SkuChange\Model\ProductApiClient\DataTransformerInterface;
use Turiac\SkuChange\Model\ProductApiClient\ProductApiClientInterface;

class ProductChangeHandler
{
    private $apiClient;
    private $dataTransformer;

    public function __construct(
        ProductApiClientInterface $apiClient,
        DataTransformerInterface $dataTransformer
    ) {
        $this->apiClient = $apiClient;
        $this->dataTransformer = $dataTransformer;
    }

    public function process($message)
    {
        $data = json_decode($message, true);
        if (is_array($data)) {
            $transformedData = $this->dataTransformer->transform($data);
            return $this->apiClient->sendProductChange($transformedData);
        } else {
            // Handle error or invalid data format
        }
    }
}