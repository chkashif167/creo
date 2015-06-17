<?php

class MST_Pdp_Block_Adminhtml_Shape extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	protected $_default_page_size = 20;
    public function __construct()
    {
		
        $this->_controller = 'adminhtml_shape';
        $this->_blockGroup = 'pdp';
        $_helper = Mage::helper('pdp');
		parent::__construct(); 
    }
	public function getImageCollectionPaging($current_page, $page_size, $url, $category){
		$collection = Mage::getModel('pdp/shapes')->getImageCollectionByCategory($category);
		$collection_counter = Mage::getModel('pdp/shapes')->getImageCollectionByCategory($category);
		$total = count($collection_counter);
		$viewPerPage = Mage::helper('pdp')->getViewPerPage();
		return Mage::helper('pdp/shape')->pagingCollection($current_page, $page_size, $viewPerPage, $collection, $total, $url, $category);
	}
}