<?php

class Stuntcoders_GoogleShopping_Block_AddForm extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_headerText = Mage::helper('stuntcoders_googleshopping')->__('Google Shopping Feed Manager');;
        parent::__construct();
        $this->setTemplate('stuntcoders/googleshopping/add.phtml');
    }

    protected function _prepareLayout()
    {
        $feed = Mage::registry('stuntcoders_googleshopping_feed');

        /** @var Mage_Adminhtml_Block_Widget_Button $saveButton */
        $saveButton = $this->getLayout()->createBlock('adminhtml/widget_button');
        $saveButton->setData(array(
            'label' =>  Mage::helper('stuntcoders_googleshopping')->__('Save'),
            'onclick' => "googleshopping_form.submit()",
            'class' => 'save'
        ));
        $this->setChild('googleshopping.savenew', $saveButton);

        if ($feed) {
            /** @var Mage_Adminhtml_Block_Widget_Button $backButton */
            $backButton = $this->getLayout()->createBlock('adminhtml/widget_button');
            $backButton->setData(array(
                    'label' =>  Mage::helper('stuntcoders_googleshopping')->__('Back'),
                    'onclick' => "setLocation('" . $this->getUrl('*/*/index') . "')",
                    'class' => 'back')
            );

            /** @var Mage_Adminhtml_Block_Widget_Button $deleteButton */
            $deleteButton = $this->getLayout()->createBlock('adminhtml/widget_button');
            $deleteButton->setData(array(
                    'label' =>  Mage::helper('stuntcoders_googleshopping')->__('Delete'),
                    'onclick' => $this->_getDeleteOnClickHandler($feed),
                    'class' => 'delete')
            );

            /** @var Mage_Adminhtml_Block_Widget_Button $generateButton */
            $generateButton = $this->getLayout()->createBlock('adminhtml/widget_button');
            $generateButton->setData(array(
                'label' =>  Mage::helper('stuntcoders_googleshopping')->__('Generate XML File'),
                'onclick' => $this->_getGenerateXmlOnClickHandler($feed),
                'class' => 'generate'
            ));

            $this->setChild('googleshopping.back', $backButton);
            $this->setChild('googleshopping.delete', $deleteButton);
            $this->setChild('googleshopping.generate', $generateButton);
        }

        $this->setChild('googleshopping_form', $this->getLayout()->createBlock('stuntcoders_googleshopping/add_form'));
    }

    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getFormHtml()
    {
        return $this->getChildHtml('googleshopping_form');
    }

    protected function _getDeleteOnClickHandler($feed)
    {
        return "setLocation('" . $this->getUrl('*/*/delete', array('id' => $feed->getId())) . "')";
    }

    protected function _getGenerateXmlOnClickHandler($feed)
    {
        return "setLocation('" . $this->getUrl('*/*/generatexml', array('id' => $feed->getId())) . "')";
    }
}
