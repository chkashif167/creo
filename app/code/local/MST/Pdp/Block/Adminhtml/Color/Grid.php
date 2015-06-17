<?php
class MST_Pdp_Block_Adminhtml_Color_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'colorGrid' );
		$this->setDefaultSort ( 'color_id' );
		$this->setDefaultDir ( 'ASC' );
		$this->setSaveParametersInSession ( true );
	}
	protected function _prepareCollection() {
		$collection = Mage::getModel ( 'pdp/color' )->getCollection ();
		$this->setCollection ( $collection );
		return parent::_prepareCollection ();
	}
	protected function _prepareColumns() {
		$this->addColumn ( 'color_id', array (
				'header' => Mage::helper ( 'pdp' )->__ ( 'ID' ),
				'align' => 'left',
				'width' => '50px',
				'index' => 'color_id' 
		) );
		$this->addColumn ( 'color_name', array (
				'header' => Mage::helper ( 'pdp' )->__ ( 'Color Name' ),
				'align' => 'left',
				'index' => 'color_name'
		) );
		$this->addColumn ( 'color_code', array (
				'header' => Mage::helper ( 'pdp' )->__ ( 'Hexcode' ),
				'align' => 'center',
				'index' => 'color_code',
		) );
		$this->addColumn ( 'color_preview', array (
				'header' => Mage::helper ( 'pdp' )->__ ( 'Color Preview' ),
				'align' => 'center',
				'index' => 'color_code',
				'renderer' => 'pdp/adminhtml_template_grid_renderer_color',
		) );
		$this->addColumn ( 'position', array (
				'header' => Mage::helper ( 'pdp' )->__ ( 'Position' ),
				'align' => 'left',
				'index' => 'position'
		) );
		$this->addColumn ( 'status', array (
				'header' => Mage::helper ( 'pdp' )->__ ( 'Status' ),
				'align' => 'left',
				'width' => '80px',
				'index' => 'status',
				'type' => 'options',
				'options' => array (
						1 => 'Enabled',
						2 => 'Disabled' 
				) 
		) );
		
		$this->addColumn ( 'action', array (
				'header' => Mage::helper ( 'pdp' )->__ ( 'Action' ),
				'width' => '100',
				'type' => 'action',
				'getter' => 'getId',
				'actions' => array (
						array (
								'caption' => Mage::helper ( 'pdp' )->__ ( 'Edit' ),
								'url' => array (
										'base' => '*/*/edit'
								),
								'field' => 'id'
						)
				),
				'filter' => false,
				'sortable' => false,
				'index' => 'stores',
				'is_system' => true
		) );
		
		$this->addExportType ( '*/*/exportCsv', Mage::helper ( 'pdp' )->__ ( 'CSV' ) );
		$this->addExportType ( '*/*/exportXml', Mage::helper ( 'pdp' )->__ ( 'XML' ) );
		
		return parent::_prepareColumns ();
	}
	protected function _prepareMassaction() {
		$this->setMassactionIdField ( 'id' );
		$this->getMassactionBlock ()->setFormFieldName ( 'color' );
		
		$this->getMassactionBlock ()->addItem ( 'delete', array (
				'label' => Mage::helper ( 'pdp' )->__ ( 'Delete' ),
				'url' => $this->getUrl ( '*/*/massDelete' ),
				'confirm' => Mage::helper ( 'pdp' )->__ ( 'Are you sure?' ) 
		) );
		$statuses = Mage::getSingleton ( 'pdp/color' )->getOptionArray ();
		array_unshift ( $statuses, array (
				'label' => '',
				'value' => '' 
		) );
		$this->getMassactionBlock ()->addItem ( 'status', array (
				'label' => Mage::helper ( 'pdp' )->__ ( 'Change status' ),
				'url' => $this->getUrl ( '*/*/massStatus', array (
						'_current' => true 
				) ),
				'additional' => array (
						'visibility' => array (
								'name' => 'status',
								'type' => 'select',
								'class' => 'required-entry',
								'label' => Mage::helper ( 'pdp' )->__ ( 'Status' ),
								'values' => $statuses 
						) 
				) 
		) );
		return $this;
	}
	public function getRowUrl($row) {
		return $this->getUrl ( '*/*/edit', array (
				'id' => $row->getId () 
		) );
	}
}