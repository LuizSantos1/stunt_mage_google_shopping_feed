<?php

class Stuntcoders_GoogleShopping_Helper_Data extends Mage_Core_Helper_Abstract
{
    const MANAGED_STOCK_CONFIG_PATH = 'stuntcoders_googleshopping/additional_options/managed_stock';

    public function getCategoriesOptions()
    {
        $categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('name', array('neq' => ''))
            ->setOrder('path', Varien_Data_Collection_Db::SORT_ORDER_ASC);

        $values = array();

        foreach ($categories as $category) {
            $values[] = array(
                'value' => $category->getId(),
                'label' => str_repeat('––', $category->getLevel()) . ' ' . $category->getName(),
            );
        }

        return $values;
    }

    public function isStockManaged()
    {
        return Mage::getStoreConfigFlag(self::MANAGED_STOCK_CONFIG_PATH);
    }
}
