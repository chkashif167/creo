<?php
class MST_Pdp_Block_Adminhtml_Importtext_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('importtext_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('pdp')->__('Import Text'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section_importtext', array(
            'label' => Mage::helper('pdp')->__('Import Text'),
            'title' => Mage::helper('pdp')->__('Import Text'),
            'content' => $this->getLayout()->createBlock('pdp/adminhtml_importtext_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}