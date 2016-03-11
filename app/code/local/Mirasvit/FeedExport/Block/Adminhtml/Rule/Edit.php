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
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Block_Adminhtml_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'rule_id';
        $this->_blockGroup = 'feedexport';
        $this->_controller = 'adminhtml_rule';

        if ($this->getRequest()->getParam('popup')) {
            $this->_removeButton('back');
            $this->_addButton(
                'close',
                array(
                    'label'   => __('Close Window'),
                    'class'   => 'cancel',
                    'onclick' => 'window.close()',
                    'level'   => -1
                )
            );
        } else {
            $this->_addButton(
                'save_and_edit_button',
                array(
                    'label'   => __('Save and Continue Edit'),
                    'onclick' => 'saveAndContinueEdit()',
                    'class'   => 'save'
                ),
                100
            );
            $this->_formScripts[] = "
                function saveAndContinueEdit() {
                    editForm.submit($('edit_form').action + 'back/edit/');
                }
            ";

            if (!Mage::registry('current_model')->getId()) {
                $this->_removeButton('save');
            }
        }

        $this->_updateButton('save', 'label', __('Save Filter'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_model')->getId() > 0) {
            $name = Mage::registry('current_model')->getName();
            return __("Edit Filter '%s'", $this->htmlEscape($name));
        } else {
            return __('Add Filter');
        }
    }
}