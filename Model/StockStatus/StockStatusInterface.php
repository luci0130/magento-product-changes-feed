<?php

namespace Turiac\SkuChange\Model\StockStatus;

interface StockStatusInterface
{
    /**
     * Retrieves the stock status for a given SKU.
     * Returns true if the product is in stock, false otherwise.
     *
     * @param string $sku The SKU of the product.
     * @return bool Stock status.
     */
    public function getStockStatus($sku);

    /**
     * Retrieves the stock quantity for a given SKU.
     *
     * @param string $sku
     * @return float Stock quantity.
     */
    public function getStockQty($sku);

    /**
     * Checks if the product is saleable for a given SKU.
     * A product is considered saleable if it's in stock and meets other conditions
     * defined in the inventory configuration.
     *
     * @param string $sku
     * @return bool Saleability status.
     */
    public function isProductSaleable($sku);

    /**
     * Optionally, if managing multiple stock sources or dealing with complex inventory logic,
     * retrieves the saleable quantity for a given SKU. This method is particularly useful
     * with MSI enabled, as it considers stock across multiple sources.
     *
     * @param string $sku
     * @return float Saleable quantity.
     */
    public function getSaleableQty($sku);
}