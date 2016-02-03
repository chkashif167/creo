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


class Mirasvit_EmailDesign_Block_Adminhtml_Design_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'design_id';
        $this->_blockGroup = 'emaildesign';
        $this->_controller = 'adminhtml_design';

        if (Mage::registry('current_model')->getId() > 0) {
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('emaildesign')->__('Save And Continue Edit'),
                'onclick'   => 'saveAjax()',
                'class'     => 'save',
            ), -100);
        }
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_model')->getId() > 0) {
            return Mage::helper('emaildesign')->__("Edit Design '%s'", $this->htmlEscape(Mage::registry('current_model')->getTitle()));
        } else {
            return Mage::helper('emaildesign')->__('Add Design');
        }
    }
}