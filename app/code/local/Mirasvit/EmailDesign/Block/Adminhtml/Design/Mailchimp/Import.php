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


class Mirasvit_EmailDesign_Block_Adminhtml_Design_Mailchimp_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct ()
    {
        parent::__construct();

        $this->_objectId   = 'design_id';
        $this->_blockGroup = 'emaildesign';
        $this->_mode       = 'import';
        $this->_controller = 'adminhtml_design_mailchimp';


        $this->_addButton('save', array(
            'label'     => Mage::helper('emaildesign')->__('Import Designs'),
            'onclick'   => 'editForm.submit();',
            'class'     => 'save',
        ), 1);

        return $this;
    }

    public function getHeaderText ()
    {
        return Mage::helper('emaildesign')->__('Import Designs');
    }

    public function getFormActionUrl()
    {
        return $this->getUrl('*/' . $this->_controller . '/doimportMailchimp');
    }
}