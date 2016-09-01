<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */


/**
 * @author Amasty
 */
class Amasty_Checkoutfees_Block_Adminhtml_Fees_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'amcheckoutfees';
        $this->_controller = 'adminhtml_fees';
        $this->_addButton('save_and_continue', array(
            'label'   => Mage::helper('salesrule')->__('Save and Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save'
        ), 10
        );
        $this->_updateButton('save', 'onclick', 'if(editForm.submit())disableButtons();');
        $this->_formScripts[] = "function saveAndContinueEdit(){ if(editForm.submit($('edit_form').action + 'continue/edit'))disableButtons();}";
        $this->_formScripts[] = "function disableButtons(){
            $$('.form-buttons button').each(function(btn){
        btn.disabled = true; $(btn).addClassName('disabled');});
        }";
        $this->_headerText    = Mage::helper('amcheckoutfees')->__('Edit fee');
    }

    public function getHeaderText()
    {
        return Mage::helper('amcheckoutfees')->__('Checkout Fees');
    }
}