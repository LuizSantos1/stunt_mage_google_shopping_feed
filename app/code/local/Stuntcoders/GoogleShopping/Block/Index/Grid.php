<?php

class Stuntcoders_GoogleShopping_Block_Index_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('google_shopping_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getModel('stuntcoders_googleshopping/feed')->getCollection());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => Mage::helper('stuntcoders_googleshopping')->__('ID'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('stuntcoders_googleshopping')->__('Title'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'title',
        ));

        $this->addColumn('description', array(
            'header' => Mage::helper('stuntcoders_googleshopping')->__('Description'),
            'align' => 'left',
            'index' => 'description',
        ));

        $this->addColumn('link', array(
            'header' => Mage::helper('stuntcoders_googleshopping')->__('Link'),
            'align' => 'left',
            'index' => 'path',
            'renderer' => 'stuntcoders_googleshopping/index_grid_renderer_link',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/new', array('id' => $row->getId()));
    }
}
