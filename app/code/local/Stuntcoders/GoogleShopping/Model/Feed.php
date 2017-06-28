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
    const PRICE_ATTRIBUTE_CODE = 'price';

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
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
     * @param DOMNode $channel
     * @param DOMDocument $doc
     * @throws Exception
     */
    protected function _prepareProductForXml($productCollection, $channel, $doc)
    {
        $attributes = json_decode($this->getAttributes(), true);

        /** @var Mage_Catalog_Model_Product $product */
        foreach ($productCollection as $product) {
            $item = $channel->appendChild($doc->createElement('item'));

            if (!count($attributes)) {
                throw new Exception(Mage::helper('stuntcoders_googleshopping')->__('No Feed Attributes defined'));
            }

            $this->_prepareAttributesForXml($attributes, $product, $item, $doc);
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
        }
    }

    /**
     * @param array $attributes
     * @param Mage_Catalog_Model_Product $product
     * @param DOMNode $item
     * @param DOMDocument $doc
     */
    protected function _prepareAttributesForXml($attributes, $product, $item, $doc)
    {
        foreach ($attributes as $name => $value) {
            $tagValue = Mage::helper('core/string')->truncate($this->_getTagValue($name, $value, $product), 4500);
            $type = $this->_getValueKey('type', $value);
            $elementValue = $this->_getValueKey('value_prefix', $value) . $tagValue;
            $tag = $doc->createElement($this->_getPrefix($value) . $name);
            $itemTag = $item->appendChild($tag);

            $data = $doc->createCDataSection($elementValue);
            if (self::PRICE_ATTRIBUTE_CODE === $name) {
                $data = $doc->createTextNode(number_format((float) $elementValue, 2)
                    . ' ' . Mage::app()->getStore()->getCurrentCurrency()->getCode());
            }

            $tag->appendChild($data);
            if (!empty($type)) {
                $itemTag->setAttribute('type', $type);
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
     * @param string $name
     * @param string $value
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function _getTagValue($name, $value, $product)
    {
        $attribute = $this->_getValueKey('attribute', $value);
        $default = $this->_getValueKey('default', $value);
        $fallback = $this->_getValueKey('fallback_attribute', $value);

        if (!empty($default) && self::PRICE_ATTRIBUTE_CODE === $name) {
            return $default;
        }

        if (!empty($this->_getAttributeText($attribute, $product))) {
            return $this->_getAttributeText($attribute, $product);
        }

        if (!empty($this->_getAttributeText($fallback, $product))) {
            return $this->_getAttributeText($fallback, $product);
        }

        return isset($default) ? $default : '';
    }

    /**
     * @param string $attribute
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function _getAttributeText($attribute, $product)
    {
        $value = '';

        if (!empty($attribute) && $product->getData($attribute)) {
            $value = $product->getData($attribute);

            if (empty($value)) {
                if ($product->getAttributeText($attribute)) {
                    $value = $product->getAttributeText($attribute);
                }
            }
        }

        return $value;
    }
}
