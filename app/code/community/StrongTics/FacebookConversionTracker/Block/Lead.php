<?php

/**
 * Facebook Lead Conversion Pixel
 * 
 * @category    StrongTics
 * @package     FacebookConversionTracker
 * @author      Issa BERTHE <issaberthet@gmail.com>
 * @copyright   Copyright (c) StrongTics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StrongTics_FacebookConversionTracker_Block_Lead extends StrongTics_FacebookConversionTracker_Block_Abstract {

    protected function _isLeadEnabled() {
        return $this->_getConfigHelper()->isLeadEnabled();
    }

    public function getLeadConversionPixelId() {
        return $this->_getConfigHelper()->getLeadConversionPixelId();
    }

    protected function _canShow() {
        if (!$this->getLeadConversionPixelId() || !$this->_isLeadEnabled() || !$this->getContactSubmitReport()) {

            return false;
        }

        return true;
    }

    public function getContactSubmitReport() {
        $session = Mage::getSingleton('core/session', array('name' => 'frontend'));
        return $session->getIsContactSubmit();
    }

    protected function _toHtml() {

        $html = parent::_toHtml();

        /*  Use to remove session info set to track contact form submit */
        if ($this->getContactSubmitReport()) {
            $this->_resetContactSubmitReport();
        }

        return $html;
    }

    protected function _resetContactSubmitReport() {
        Mage::getSingleton('core/session')->setIsContactSubmit(0);
    }

}
