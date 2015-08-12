<?php

class MST_Pdp_Block_Adminhtml_Shapecate_Edit_Tab_Artwork extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('shapeGrid');
		$this->setUseAjax(true);
		$this->setDefaultSort('id');
		$this->setDefaultFilter(array('in_products' => 1)); // By default we have added a filter for the rows, that in_products value to be 1
		$this->setSaveParametersInSession(false);  //Dont save paramters in session or else it creates problems
	}
    protected function _prepareCollection()
	{
		$collection = Mage::getModel('pdp/shapes')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _addColumnFilterToCollection($column)
	{
		// Set custom filter for in product flag
		//$categoryId = $this->getRequest()->getParam('id');
		if ($column->getId() == 'in_products') {
			$ids = $this->_getSelectedImages();
			if (empty($ids)) {
				$ids = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('id', array('in'=>$ids));
			} else {
				if($ids) {
					$this->getCollection()->addFieldToFilter('id', array('nin'=>$ids));
				}
			}
		} else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}

	protected function _prepareColumns()
	{
		$this->addColumn('in_products', array(
			'header_css_class'  => 'a-center',
			'type'              => 'checkbox',
			//'field_name'        => 'artworks[]',
			'values'            => $this->_getSelectedImages(),
			'align'             => 'center',
			'index'             => 'id'
		));
		$this->addColumn('shape_id', array(
			'header'    => Mage::helper('pdp')->__('ID'),
			'width'     => '50px',
			'index'     => 'id',
			'type'  => 'number',
		));
		$this->addColumn('filename', array(
			'header'    => Mage::helper('pdp')->__('Shape'),
			'index'     => 'filename',
			'renderer' 	=> 'pdp/adminhtml_template_grid_renderer_shape',
		));
		$this->addColumn('position', array(
            'header'            => Mage::helper('pdp')->__('Position'),
            'name'              => 'position',
            'width'             => 60,
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'editable'          => true,
            'edit_only'         => true
        ));
		/* $categories = Mage::getModel('pdp/artworkcate')->getCategoryOptions();
		$this->addColumn('category', array(
			'header'	=> Mage::helper('pdp')->__('Category'),
			'width'     => '150',
			'index'     => 'category',
			'type'      => 'options',
			'options'	=> $categories
		)); */
		return parent::_prepareColumns();
	}
	protected function _getSelectedImages()   // Used in grid to return selected images values.
	{
		$images = array_keys($this->getSelectedImages());
		return $images;
	}
	public function getGridUrl()
	{
		return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/imagegrid', array('_current'=>true));
	}
	public function getSelectedImages()
	{
		$tm_id = $this->getRequest()->getParam('id');
		if(!isset($tm_id)) {
			$tm_id = 0;
		}
		$collection = Mage::getModel('pdp/shapes')->getCollection();
		$collection->addFieldToFilter('category',$tm_id);
		$custIds = array();
		foreach($collection as $obj){
			$custIds[$obj->getId()] = array('position' => $obj->getPosition());
		}
		return $custIds;
	}
}