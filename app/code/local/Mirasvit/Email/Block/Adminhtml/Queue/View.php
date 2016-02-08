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


class Mirasvit_Email_Block_Adminhtml_Queue_View extends Mage_Adminhtml_Block_Template
{
    public function _prepareLayout()
    {
        $this->setTemplate('mst_email/queue/view.phtml');

        return parent::_prepareLayout();
    }

    public function getModel()
    {
        return Mage::registry('current_model');
    }

    public function getHeaderText()
    {
        return Mage::helper('email')->__("Email to '%s'", $this->htmlEscape($this->getModel()->getRecipientEmail()));
    }

    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/drop', array('_current' => true));
    }

    public function getBackUrl()
    {
        return $this->getUrl('adminhtml/email_queue/index');
    }
}