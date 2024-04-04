<?php

namespace Turiac\SkuChange\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class ProductAttributesRows extends AbstractFieldArray
{
    /**
     * @var ProductAttributeColumn
     */
    private $attributesRenderer;

    /**
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn('product_attribute', [
            'label'    => __('Product Attributes'),
            'class'    => 'required-entry',
            'renderer' => $this->getAttributesRenderer()
        ]);

        $this->addColumn('api_attribute', [
            'label' => __('Api Attributes')
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $productAttribute = $row->getProductAttribute();
        if ($productAttribute !== null) {
            $options['option_' . $this->getAttributesRenderer()->calcOptionHash($productAttribute)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return ProductAttributeColumn
     * @throws LocalizedException
     */
    private function getAttributesRenderer(): ProductAttributeColumn
    {
        if (!$this->attributesRenderer) {
            $this->attributesRenderer = $this->getLayout()->createBlock(
                ProductAttributeColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->attributesRenderer;
    }
}
