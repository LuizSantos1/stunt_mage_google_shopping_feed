<?php

class Stuntcoders_GoogleShopping_Model_Resource_Feed extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('stuntcoders_googleshopping/feed', 'id');
    }
}
