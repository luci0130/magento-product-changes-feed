# Mage2 Module Turiac SkuChange

    ``turiac/module-skuchange``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
The SkuChange module is designed to handle SKU updates dynamically across Magento 2's product catalog. This module ensures that updated stock levels, attribute changes, and stock status (including out-of-stock status after updates from various operations like order placement, admin saves, imports, etc.) are accurately reported. The module functions according to the following specifications:

### Data Exchange Format
The module sends data in the following JSON format:
```json
{
  "sku": "WS08-S-Red",
  "updatedData": {
    "stock_qty": 99,
    "stock_status": "In Stock",
    "is_salable": true,
    "saleable_qty": 99
  },
  "store": 1,
  "source": "admin_order_shipment_save",
  "time": "2024-04-13 13:42:50",
  "productchanges_id": "100"
}
```

### Considerations
- If a simple product is updated and the parent product (configurable, bundle, group) is modified, the update must also be sent for the parent. Ex: if all simple products become out of stock, an update for parent should be sent.
- If the third party service is not working, product updates must still be sent upon third party service restart and keep the same order.
- Magento has two modes of stock management: Legacy (Single Source Inventory) before Magento 2.3 and Multi Source Inventory starting from Magento 2.3.


### How I build the module:

#### 1. Identifying attribute update operations:

Update attribute:
- admin save
- product import
- API calls
- external module

Event that includes these updates: `catalog_product_save_after`

Update stock:
- admin save
- import
- place order / create shipment
- return order
- cancel order
- API calls
- external module

Events:
`cataloginventory_stock_item_save_after`

Plugins:
- `Magento\CatalogInventory\Model\Stock\StockItemRepository` - afterSave - update stock status for the parent product
- `Magento\Inventory\Model\SourceItem\Command\DecrementSourceItemQty` - afterExecute - create shipment

2. Creating observers/plugins:
3. Create a service that adds changes made to products to the database and sends requests to third parties.
4. Use a configuration to decide whether or not to send requests to third parties to modify the attribute list and enable tracking of stock and its status.
5. Modify the observer to also check stock at `catalog_product_save_after`.
6. Create an observer to monitor stock changes - `cataloginventory_stock_item_save_after`.
7. Create a plugin that monitors status changes of the parent product when the child product enters or exits stock - `StockItemRepository - afterSave`.
8. Ensure that the request to third parties is executed and that the order of product attribute changes is maintained.
9. Retry mechanism and queue restart when an incorrect response is received, create a new retry queue that will hold the last messages that were not sent, at the queue restart we ensure that the retry queue is executed first, thus maintaining the order of messages. Ex: We receive an incorrect response, republish the message in the retry queue and then stop the consumer of the main queue. Upon restart, we ensure that the retry queue is executed first and then the main queue.

## Installation

 - Unzip the zip file in `app/code/Turiac`
 - Enable the module by running `php bin/magento module:enable Turiac_SkuChange`
 - Apply database updates by running `php bin/magento setup:upgrade`
 - Flush the cache by running `php bin/magento cache:flush`

### Configuration



### Product Changes Grid



### API call





