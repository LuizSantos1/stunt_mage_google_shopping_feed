<?php

class Stuntcoders_GoogleShopping_Block_Index_Grid_Renderer_Link
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $file = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $row->getPath();
        $caption = Mage::helper('stuntcoders_googleshopping')->__($row->getPath());

        $validator = new Zend_Validate_File_Exists();
        $validator->addDirectory(Mage::getBaseDir() . DS);

        if (!$validator->isValid($row->getPath())) {
            $file = ' ';
            $caption = Mage::helper('stuntcoders_googleshopping')->__('Xml file is not generated');
        }

        $this->getColumn()->setActions(
            array(array(
                'url' => $file,
                'caption' => $caption,
                ))
        );

        return parent::render($row);
    }
}
