<?php

namespace Turiac\SkuChange\Model\ProductApiClient;

class ProductApiClient implements ProductApiClientInterface
{
    protected $httpClientFactory;
    protected $logger;

    public function __construct(
        \Magento\Framework\HTTP\ClientFactory $httpClientFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->logger = $logger;
    }

    public function sendProductChange(array $data)
    {
        $client = $this->httpClientFactory->create();
        $uri = 'https://24a22575f2b9483b9188d3724f692581.api.mockbin.io/';
        $body = json_encode($data);

        $client->setHeaders(['Content-Type' => 'application/json']);

        $client->post($uri, $body);

        $responseBody = json_decode($client->getBody(), true);
        return [
            'httpStatusCode' => $client->getStatus(),
            'body' => $responseBody
        ];
    }
}
