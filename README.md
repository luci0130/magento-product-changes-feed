# Mage2 Module Turiac SkuChange

    ``turiac/module-skuchange``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
SkuChange

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Turiac`
 - Enable the module by running `php bin/magento module:enable Turiac_SkuChange`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require turiac/module-skuchange`
 - enable the module by running `php bin/magento module:enable Turiac_SkuChange`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - Cronjob
	- turiac_skuchange_sendproductchanges

 - Observer
	- source_items_save_after > Turiac\SkuChange\Observer\Source\ItemsSaveAfter

 - Observer
	- inventory_source_deduction > Turiac\SkuChange\Observer\Inventory\SourceDeduction

 - Observer
	- catalog_product_save_after > Turiac\SkuChange\Observer\Catalog\ProductSaveAfter


## Attributes



