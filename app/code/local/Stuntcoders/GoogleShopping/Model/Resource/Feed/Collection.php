<?php

class Stuntcoders_GoogleShopping_Model_Resource_Feed_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('stuntcoders_googleshopping/feed');
    }
}
