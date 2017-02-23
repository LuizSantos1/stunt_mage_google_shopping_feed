<?php

/**
 * @method Stuntcoders_GoogleShopping_Model_Feed setPath(string $type)
 * @method string getPath()
 * @method Stuntcoders_GoogleShopping_Model_Feed setTitle(string $type)
 * @method string getTitle()
 * @method Stuntcoders_GoogleShopping_Model_Feed setDescription(string $type)
 * @method string getDescription()
 * @method Stuntcoders_GoogleShopping_Model_Feed setCategories(string $type)
 * @method string getCategories()
 * @method Stuntcoders_GoogleShopping_Model_Feed setAttributes(string $type)
 * @method string getAttributes()
 */
class Stuntcoders_GoogleShopping_Model_Feed extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('stuntcoders_googleshopping/feed');
    }

    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();
        if (!$this->getPath()) {
            $errors[] = Mage::helper('stuntcoders_googleshopping')->__('Path is mandatory');
        }

        return $errors;
    }

    public function generateXml()
    {
        $productCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->joinField(
                'category_id', 'catalog/category_product', 'category_id',
                'product_id=entity_id', null, 'left'
            )
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('category_id', array('in' => explode(',', $this->getCategories())));

        if (Mage::helper('stuntcoders_googleshopping')->isStockManaged()) {
            $productCollection->joinField(
                'is_in_stock',
                'cataloginventory/stock_item',
                'is_in_stock',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            )->addAttributeToFilter('is_in_stock', array('eq' => 1));
        }

        $productCollection
            ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addAttributeToFilter(
                'visibility',
                array('in' => array(
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                )
            )->groupByAttribute('entity_id');

        $doc = new DOMDocument('1.0');
        $doc->formatOutput = true;

        $rss = $doc->appendChild($doc->createElement('rss'));
        $rss->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
        $rss->setAttribute('version', '2.0');
        $channel = $rss->appendChild($doc->createElement('channel'));
        $channel->appendChild($doc->createElement('title', $this->getTitle()));
        $channel->appendChild($doc->createElement('link', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)));
        $channel->appendChild($doc->createElement('description', $this->getDescription()));

        if ($productCollection->getSize()) {
            $this->_prepareProductForXml($productCollection, $channel, $doc);
        }

        return $doc->saveXML();
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection
     * @param DOMNode $channel
     * @param DOMDocument $doc
     */
    protected function _prepareProductForXml($productCollection, $channel, $doc)
    {
        $attributes = json_decode($this->getAttributes(), true);

        /** @var Mage_Catalog_Model_Product $product */
        foreach ($productCollection as $product) {
            $item = $channel->appendChild($doc->createElement('item'));

            $item->appendChild($doc->createElement('g:id', $product->getSku()));

            if (count($attributes)) {
                $this->_prepareAttributesForXml($attributes, $product, $item, $doc);
            }

            $item->appendChild($doc->createElement('g:link', $product->getUrlInStore()));

            try {
                $item->appendChild($doc->createElement(
                    'g:image_link',
                    Mage::helper('catalog/image')->init($product, 'image')->resize(800)
                ));
            } catch (Exception $e) {
                $item->appendChild($doc->createElement(
                    'g:image_link',
                    Mage::getDesign()->getSkinUrl(
                        'images/catalog/product/placeholder/image.jpg',
                        array('_area' => 'frontend')
                    )
                ));
            };

            $item->appendChild($doc->createElement(
                'g:price',
                number_format((float) $product->getPrice(), 2)
                . ' ' . Mage::app()->getStore()->getCurrentCurrency()->getCode()
            ));
        }
    }

    /**
     * @param array $attributes
     * @param Mage_Catalog_Model_Product $product
     * @param DOMDocument $item
     * @param DOMDocument $doc
     */
    protected function _prepareAttributesForXml($attributes, $product, $item, $doc)
    {
        foreach ($attributes as $name => $value) {
            $prefix = $this->_getPrefix($value);
            $valuePrefix = $this->_getValueKey('value_prefix', $value);
            $type = $this->_getValueKey('type', $value);
            $attribute = $this->_getValueKey('attribute', $value);
            $default = $this->_getValueKey('default', $value);

            $tagValue = $this->_getTagValue($default, $attribute, $product);
            if (empty($tagValue)) {
                continue;
            }

            $itemTag = $item->appendChild($doc->createElement($prefix . $name, $valuePrefix . $tagValue));

            if (!empty($type)) {
                $itemTag->setAttribute('type', $value['type']);
            }
        }
    }

    /**
     * @param array $value
     * @param string $valueKey
     * @return string
     */
    protected function _getValueKey($valueKey, $value)
    {
        if (array_key_exists($valueKey, $value) && !empty($value[$valueKey])) {
            return $value[$valueKey];
        }

        return '';
    }

    /**
     * @param string $value
     * @return string
     */
    protected function _getPrefix($value)
    {
        if (array_key_exists('prefix', $value) && !empty($value['prefix'])) {
            return $value['prefix'] . ':';
        }

        return '';
    }

    /**
     * @param string $defaultValue
     * @param string $attribute
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function _getTagValue($defaultValue, $attribute, $product)
    {
        $tagValue = isset($defaultValue) ? $defaultValue : '';

        if (!empty($attribute) && $product->getData($attribute)) {
            $tagValue = $product->getData($attribute);
            if ($product->getAttributeText($attribute)) {
                $tagValue = $product->getAttributeText($attribute);
            }
        }

        return Mage::helper('core/string')->truncate($tagValue, 400);
    }
}
