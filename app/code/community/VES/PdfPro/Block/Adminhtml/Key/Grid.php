<?php

class VES_PdfPro_Block_Adminhtml_Key_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('apiKeyGrid');
      $this->setSaveParametersInSession(true);
      $this->setDefaultSort('priority');
      $this->setDefaultDir('ASC');
  }

  protected function _prepareCollection()
  {
	  $collection = Mage::getModel('pdfpro/key')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
  	  Mage::dispatchEvent('ves_pdfpro_grid_prepare_columns_before',array('block'=>$this));
      $this->addColumn('entity_id', array(
          'header'    => Mage::helper('pdfpro')->__('ID'),
          'align'     =>'left',
          'index'     => 'entity_id',
      	  'width'	  => '50px',
      ));  
  	  $this->addColumn('api_key', array(
          'header'    => Mage::helper('pdfpro')->__('API Key'),
          'align'     =>'left',
          'index'     => 'api_key',
      ));
		/**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'store_ids',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
            	'filter'		=> false,
            	'renderer'	=> new VES_PdfPro_Block_Adminhtml_Key_Grid_Renderer_Store(),
            ));
        }
      	$this->addColumn('customer_group_ids', array(
      		'header'        => Mage::helper('cms')->__('Customer Group'),
       		'index'         => 'customer_group_ids',
       		'type'          => 'store',
     		'store_all'     => true,
        	'store_view'    => true,
       		'sortable'      => false,
           	'filter'		=> false,
          	'renderer'	=> new VES_PdfPro_Block_Adminhtml_Key_Grid_Renderer_Group(),
  		));
      $this->addColumn('comment', array(
          'header'    => Mage::helper('pdfpro')->__('Comment'),
          'align'     =>'left',
          'index'     => 'comment',
      	  'width'	  => '400px',
      ));
      
      $this->addColumn('priority', array(
          'header'    => Mage::helper('pdfpro')->__('Priority'),
          'align'     =>'center',
          'index'     => 'priority',
      	  'width'	  => '50px',
      ));
      
      Mage::dispatchEvent('ves_pdfpro_grid_prepare_columns_after',array('block'=>$this));
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