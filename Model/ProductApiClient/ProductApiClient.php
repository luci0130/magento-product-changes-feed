<?php

namespace Turiac\SkuChange\Model\ProductApiClient;

use Magento\Framework\HTTP\ClientFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;

class ProductApiClient implements ProductApiClientInterface
{
    private string $urlApi;
    private ClientFactory $httpClientFactory;
    private LoggerInterface $logger;
    private int $maxAttempts;
    private int $initialRetryDelay;

    public function __construct(
        ClientFactory $httpClientFactory,
        LoggerInterface $logger,
        string $urlApi = 'https://24a22575f2b9483b9188d3724f692581.api.mockbin.io/',
        int $maxAttempts = 3,
        int $initialRetryDelay = 1000
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->logger = $logger;
        $this->urlApi = $urlApi;
        $this->maxAttempts = $maxAttempts;
        $this->initialRetryDelay = $initialRetryDelay;
    }

    public function sendProductChange(array $data): array
    {
        $body = json_encode($data);
        if ($body === false) {
            $errorMsg = 'JSON encoding error: ' . json_last_error_msg();
            $this->logger->error($errorMsg);
            throw new LocalizedException(__($errorMsg));
        }

        $client = $this->httpClientFactory->create();
        $client->setHeaders(['Content-Type' => 'application/json']);
        $retryDelay = $this->initialRetryDelay;

        for ($attempts = 0; $attempts < $this->maxAttempts; $attempts++) {
            try {
                $client->post($this->urlApi, $body);
                $statusCode = $client->getStatus();
                if ($this->isSuccessfulResponse($statusCode)) {
                    return $this->processResponse($client->getBody());
                }
                throw new \Exception("Received non-successful HTTP status code: $statusCode.");
            } catch (\Exception $e) {
                $this->logger->error("Attempt " . ($attempts + 1) . ": " . $e->getMessage());
                if ($attempts === $this->maxAttempts - 1) {
                    throw new LocalizedException(__("Failed after {$this->maxAttempts} attempts."), 0, $e);
                }
                usleep($retryDelay * 1000);
                $retryDelay *= 2;
            }
        }

        return [];
    }

    private function isSuccessfulResponse(int $statusCode): bool
    {
        return $statusCode >= 200 && $statusCode < 300;
    }

    private function processResponse(string $responseBody): array
    {
        $decodedBody = json_decode($responseBody, true);
        if ($decodedBody === null) {
            $errorMsg = 'Error decoding JSON response: ' . json_last_error_msg();
            $this->logger->error($errorMsg);
            throw new LocalizedException(__($errorMsg));
        }
        if (!$this->isSuccessfulResponseBody($decodedBody)) {
            throw new LocalizedException(__('Received successful HTTP status code but the message was not processed properly.'));
        }
        return [
            'httpStatusCode' => $this->httpClientFactory->create()->getStatus(),
            'body' => $decodedBody
        ];
    }

    private function isSuccessfulResponseBody(array $responseBody): bool
    {
        return isset($responseBody['processing_status']) && $responseBody['processing_status'] === 'success';
    }
}
