<?php

class MST_Pdp_Block_Adminhtml_Color_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('color_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('pdp')->__('General Information'));
    }
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => Mage::helper('pdp')->__('Color Details'),
            'title' => Mage::helper('pdp')->__('Color Details'),
            'content' => $this->getLayout()->createBlock('core/template')->setTemplate('pdp/color/add_color.phtml')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}