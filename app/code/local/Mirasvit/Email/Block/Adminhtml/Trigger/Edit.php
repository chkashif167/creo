<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Email_Block_Adminhtml_Trigger_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'trigger_id';
        $this->_blockGroup = 'email';
        $this->_controller = 'adminhtml_trigger';

        $this->_addButton('saveandcontinue', array(
            'label'   => Mage::helper('email')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save',
        ), -100);

        if (Mage::registry('current_model')->getId() > 0) {
            $this->_addButton('saveandsend', array(
                'label'   => Mage::helper('email')->__('Send Test Email'),
                'onclick' => 'saveAndSend(this, false, \''.Mage::getSingleton('email/config')->getTestEmail().'\')',
            ), -100);
        }

        $this->_formScripts[] = "
            function saveAndContinueEdit()
            {
                editForm.submit($('edit_form').action + 'back/edit/');
            }

            function saveAndGenerate()
            {
                editForm.submit($('edit_form').action + 'back/generate/');
            }
        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_model')->getId() > 0) {
            return Mage::helper('email')->__("Edit Trigger '%s'", $this->htmlEscape(Mage::registry('current_model')->getTitle()));
        } else {
            return Mage::helper('email')->__('Add Trigger');
        }
    }
}