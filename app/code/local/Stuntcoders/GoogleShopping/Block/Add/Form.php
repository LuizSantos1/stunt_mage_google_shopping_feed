<?php

class Stuntcoders_GoogleShopping_Block_Add_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'googleshopping_form',
            'name' => 'googleshopping_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $data = array();
        if (Mage::registry('stuntcoders_googleshopping_feed')) {
            $data = Mage::registry('stuntcoders_googleshopping_feed')->getData();
        }

        $fieldset = $form->addFieldset('googleshopping_form', array(
            'legend' => Mage::helper('stuntcoders_googleshopping')->__('Google Shopping Feed')
        ));

        $fieldset->addField('path', 'text', array(
            'label' => Mage::helper('stuntcoders_googleshopping')->__('Path for Feed .xml'),
            'name' => 'path',
            'required'  => true,
        ));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('stuntcoders_googleshopping')->__('Feed Title'),
            'name' => 'title',
            'required'  => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('stuntcoders_googleshopping')->__('Feed Description'),
            'name' => 'description',
            'required'  => true,
        ));

        $fieldset->addField('categories', 'multiselect', array(
            'label' => Mage::helper('stuntcoders_googleshopping')->__('Feed Categories'),
            'name' => 'categories',
            'values' => Mage::helper('stuntcoders_googleshopping')->getCategoriesOptions(),
            'required'  => true,
        ));

        $fieldset->addField('attributes', 'textarea', array(
            'label' => Mage::helper('stuntcoders_googleshopping')->__('Feed Attributes'),
            'name' => 'attributes',
            'style' => 'width:500px; height:500px',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        $form->setValues($data);
        return parent::_prepareForm();
    }
}
