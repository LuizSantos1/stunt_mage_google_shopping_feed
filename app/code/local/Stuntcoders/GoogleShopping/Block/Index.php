<?php

class Stuntcoders_GoogleShopping_Block_Index extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'stuntcoders_googleshopping';
        $this->_controller = 'index';
        $this->_headerText = $this->__('Google Shopping Feed');

        return parent::__construct();
    }
}
