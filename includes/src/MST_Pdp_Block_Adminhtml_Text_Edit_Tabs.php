<?php

class MST_Pdp_Block_Adminhtml_Text_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('text_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('pdp')->__('General Information'));
    }
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => Mage::helper('pdp')->__('Text Details'),
            'title' => Mage::helper('pdp')->__('Text Details'),
            'content' => $this->getLayout()->createBlock('pdp/adminhtml_text_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}