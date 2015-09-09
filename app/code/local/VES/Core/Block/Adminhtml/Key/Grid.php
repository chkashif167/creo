<?php

class VES_Core_Block_Adminhtml_Key_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('apiKeyGrid');
      $this->setSaveParametersInSession(true);
      $this->setDefaultSort('key_id');
      $this->setDefaultDir('ASC');
  }

  protected function _prepareCollection()
  {
	  $collection = Mage::getModel('ves_core/key')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      /*$this->addColumn('key_id', array(
          'header'    => Mage::helper('ves_core')->__('ID'),
          'align'     =>'left',
          'index'     => 'key_id',
      	  'width'	  => '50px',
      ));  */
  	  $this->addColumn('license_key', array(
          'header'    => Mage::helper('ves_core')->__('License Key'),
          'align'     =>'left',
          'index'     => 'license_key',
  	  	  'width'	  => '300px',
      ));
      $this->addColumn('license_info', array(
          'header'    => Mage::helper('ves_core')->__('License Information'),
          'align'     =>'left',
          'index'     => 'license_info',
      	  'sortable'	=> false,
      	  'renderer'  => new VES_Core_Block_Adminhtml_Key_Grid_Renderer_License(),
      ));
      
      return parent::_prepareColumns();
  }
  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
  protected function _prepareMassaction()
  {
      return $this;
  }
}