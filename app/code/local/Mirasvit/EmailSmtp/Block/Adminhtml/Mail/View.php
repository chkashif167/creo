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



class Mirasvit_EmailSmtp_Block_Adminhtml_Mail_View extends Mage_Adminhtml_Block_Template
{
    public function _prepareLayout()
    {
        $this->setTemplate('mst_emailsmtp/mail/view.phtml');

        return parent::_prepareLayout();
    }

    public function getModel()
    {
        return Mage::registry('current_model');
    }

    public function getHeaderText()
    {
        return Mage::helper('emailsmtp')->__("Email to '%s'", $this->htmlEscape($this->getModel()->getToEmail()));
    }

    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview', array('_current' => true));
    }

    public function getBackUrl()
    {
        return $this->getUrl('adminhtml/emailsmtp_mail/index');
    }
}