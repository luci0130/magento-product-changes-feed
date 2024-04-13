<?php

namespace Turiac\SkuChange\Model\ProductChangesQueue;

use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Turiac\SkuChange\Model\ProductApiClient\DataTransformerInterface;
use Turiac\SkuChange\Model\ProductApiClient\ProductApiClientInterface;

class ProductChangeHandler
{
    /**
     * @var ProductApiClientInterface
     */
    private $apiClient;

    /**
     * @var DataTransformerInterface
     */
    private $dataTransformer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ProductApiClientInterface $apiClient
     * @param DataTransformerInterface $dataTransformer
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductApiClientInterface $apiClient,
        DataTransformerInterface $dataTransformer,
        LoggerInterface $logger
    ) {
        $this->apiClient = $apiClient;
        $this->dataTransformer = $dataTransformer;
        $this->logger = $logger;
    }

    /**
     * @throws LocalizedException
     */
    public function process($message)
    {
        $data = json_decode($message, true);
        if (!is_array($data)) {
            $this->logger->error('Invalid message format received', ['message' => $message]);
            throw new LocalizedException(__('Invalid message format'));
        }

        try {
            $transformedData = $this->dataTransformer->transform($data);
            return $this->apiClient->sendProductChange($transformedData);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send product change after retries', ['exception' => $e->getMessage()]);
            throw new LocalizedException(__("Failed to process product change: '%1'. ", $e->getMessage()));
        }
    }
}