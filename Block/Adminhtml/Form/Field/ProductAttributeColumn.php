<?php

namespace Turiac\SkuChange\Block\Adminhtml\Form\Field;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class ProductAttributeColumn extends Select
{
    /**
     * @var CollectionFactory
     */
    protected $productAttributeCollection;

    /**
     * Constructor
     *
     * @param Context $context
     * @param CollectionFactory $productAttributeCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productAttributeCollection,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productAttributeCollection = $productAttributeCollection;
    }

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    private function getSourceOptions(): array
    {
        $collection = $this->productAttributeCollection->create();
        $collection->addVisibleFilter();

        $attributesArray = [];
        foreach ($collection as $attribute) {
            $label = $attribute->getFrontendLabel();
            if ($label) {
                $attributesArray[] = [
                    'label' => __($label),
                    'value' => $attribute->getAttributeCode()
                ];
            }
        }

        return $attributesArray;
    }

}