<?php

namespace Turiac\SkuChange\Model\StockStatus;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManagerInterface;

class StockStatusFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * Constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleManager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Create StockStatusInterface instance.
     *
     * @return StockStatusInterface
     */
    public function create(): StockStatusInterface
    {
        if ($this->moduleManager->isEnabled('Magento_Inventory')) {
            // MSI is enabled, instantiate the MSI-based class
            return $this->objectManager->create(MsiStockStatus::class);
        } else {
            // MSI is not enabled, use the legacy StockRegistry class
            return $this->objectManager->create(StockStatus::class);
        }
    }
}
