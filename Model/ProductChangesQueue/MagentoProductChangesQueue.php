<?php

namespace Turiac\SkuChange\Model\ProductChangesQueue;

use Magento\Framework\MessageQueue\PublisherInterface;
use Psr\Log\LoggerInterface;

class MagentoProductChangesQueue implements ProductChangesQueueInterface
{
    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param PublisherInterface $publisher
     * @param LoggerInterface $logger
     */
    public function __construct(
        PublisherInterface $publisher,
        LoggerInterface $logger
    ) {
        $this->publisher = $publisher;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function addToQueue(array $data): bool
    {
        try {
            $this->publisher->publish('product.change.update', json_encode($data));
            $this->logger->info('Product change queued successfully', $data);
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Error queuing product change: ' . $e->getMessage());
            return false;
        }
    }
}