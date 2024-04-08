<?php
/**
 * Copyright © Turiac All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Turiac\SkuChange\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

class ProductChangesConfig
{
    const XML_PATH_FEED = 'feed/changes/';

    /**
     * @var Json
     */
    protected $serialize;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Json $serialize
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serialize = $serialize;
    }

    public function isEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_FEED . 'enable',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isStockTrackingEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_FEED . 'enable_stock_tracking',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve the configured product fields.
     *
     * @return array
     */
    public function getFields($storeId = null): array
    {
        $productAttributes = $this->getProductAttributes($storeId);
        if ($productAttributes && is_array($productAttributes)) {
            return array_column($productAttributes, 'product_attribute');
        }
        return $this->getDefaultFields();
    }

    /**
     * Fetch product attributes configuration.
     *
     * @param int|null $storeId
     * @return array
     */
    public function getProductAttributes($storeId = null)
    {
        $configValue = $this->scopeConfig->getValue(
            self::XML_PATH_FEED . 'product_attributes',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (is_array($configValue)) {
            return $configValue;
        }

        if (!empty($configValue) && is_string($configValue)) {
            try {
                $result = $this->serialize->unserialize($configValue);
                return is_array($result) ? $result : [];
            } catch (\Exception $e) {
                return [];
            }
        }

        return [];
    }

    /**
     * Provides default fields if no custom configuration is found.
     *
     * @return array
     */
    private function getDefaultFields()
    {
        return [
            'name',
            'description',
            'short_description'
        ];
    }
}
