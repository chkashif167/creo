<?php
class VES_PdfPro_Block_Adminhtml_Key extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_key';
		$this->_blockGroup = 'pdfpro';
		$this->_headerText = Mage::helper('pdfpro')->__('API Key Manager');
		parent::__construct();
		$this->_addButton('check_for_update', array(
            'label'     => Mage::helper('pdfpro')->__('Check For Upgrades'),
            'onclick'   => 'setLocation(\'' . $this->getCheckUpdateUrl() .'\')',
            'class'     => 'loading',
        ));
	}
	
	public function setHeaderText($text){
		$this->_headerText = $text;
	}

	public function getCheckUpdateUrl(){
		return $this->getUrl('pdfpro_cp/adminhtml_key/checkforupdate');
	}
}