<?php
class MST_Pdp_Block_Adminhtml_Importcolor_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('importcolor_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('pdp')->__('Import Color'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section_import', array(
            'label' => Mage::helper('pdp')->__('Import Color'),
            'title' => Mage::helper('pdp')->__('Import Color'),
            'content' => $this->getLayout()->createBlock('pdp/adminhtml_importcolor_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}