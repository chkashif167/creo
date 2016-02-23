<?php

class Tentura_Ngroups_Block_Adminhtml_Ngroups_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('ngroupsGrid');
      $this->setDefaultSort('ngroups_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('ngroups/ngroups')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('ngroups_id', array(
          'header'    => Mage::helper('ngroups')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'ngroups_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('ngroups')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
	
	  $this->addColumn('number', array(
          'header'    => Mage::helper('ngroups')->__('Number of users'),
          'align'     => 'left',
          'renderer'  => 'ngroups/adminhtml_ngroups_render_number',
          'index'     => 'number',
      ));

      $this->addColumn('created_time', array(
          'header'    => Mage::helper('ngroups')->__('Creation Time'),
          'align'     =>'left',
          'index'     => 'created_time',
          'type'    =>'datetime',
      ));


        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('ngroups')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('ngroups')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('ngroups')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('ngroups')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('ngroups_id');
        $this->getMassactionBlock()->setFormFieldName('ngroups');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('ngroups')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('ngroups')->__('Are you sure?')
        ));

        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}