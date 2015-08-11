<?php

class MST_Pdp_Block_Adminhtml_Pdp extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	protected $_default_page_size = 20;
    public function __construct()
    {
		
        $this->_controller = 'adminhtml_pdp';
        $this->_blockGroup = 'pdp';
        $_helper = Mage::helper('pdp');
        /*
		$this->_headerText = Mage::helper('pdp')->__('');
        $this->_addButtonLabel = Mage::helper('pdp')->__('Add Design');
        $this->_addButton('mst_reset', array( 'label' => Mage::helper('adminhtml')->__('Add New Design'), 'class' => 'reset scalable', 'id'=>'reset_menu', 'onclick'=>"location.reload()" ));
        $this->_addButton('save', array( 'label' => Mage::helper('adminhtml')->__('Save Item'), 'class' => 'save scalable', 'id'=>'save_menu', 'onclick'=>"editForm.submit();" ));
        */
		parent::__construct(); 
    }
	public function getImageCollectionPaging($current_page, $page_size, $url, $category){
		$collection = Mage::getModel('pdp/pdp')->getImageCollectionByCategory($category);
		$collection_counter = Mage::getModel('pdp/pdp')->getImageCollectionByCategory($category);
		$total = count($collection_counter);
		$viewPerPage = Mage::helper('pdp')->getViewPerPage();
		return Mage::helper('pdp')->pagingCollection($current_page, $page_size, $viewPerPage, $collection, $total, $url, $category);
	}
}