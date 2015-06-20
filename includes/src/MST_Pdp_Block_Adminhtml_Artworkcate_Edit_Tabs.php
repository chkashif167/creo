<?php

class MST_Pdp_Block_Adminhtml_Artworkcate_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('artworkcate_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('pdp')->__('Artwork Category Information'));
    }
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => Mage::helper('pdp')->__('Category Details'),
            'title' => Mage::helper('pdp')->__('Category Details'),
            'content' => $this->getLayout()->createBlock('pdp/adminhtml_artworkcate_edit_tab_form')->toHtml(),
        ));
		
		$this->addTab('form_section_artwork', array(
            'label' => Mage::helper('pdp')->__('Manage Artworks'),
            'title' => Mage::helper('pdp')->__('Manage Artworks'),
            'url'       => $this->getUrl('*/*/image', array('_current' => true)),
			'class'     => 'ajax',
        ));
        return parent::_beforeToHtml();
    }
}