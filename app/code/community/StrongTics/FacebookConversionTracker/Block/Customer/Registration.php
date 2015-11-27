<?php

/**
 * Facebook Registration Conversion Pixel
 * 
 * @category    StrongTics
 * @package     FacebookConversionTracker
 * @author      Issa BERTHE <issaberthet@gmail.com>
 * @copyright   Copyright (c) StrongTics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StrongTics_FacebookConversionTracker_Block_Customer_Registration extends StrongTics_FacebookConversionTracker_Block_Abstract {

    protected function _isRegistrationEnabled() {
        return $this->_getConfigHelper()->isRegistrationEnabled();
    }

    public function getRegistrationConversionPixelId() {
        return $this->_getConfigHelper()->getRegistrationConversionPixelId();
    }

    protected function _canShow() {
        if (!$this->getRegistrationConversionPixelId() 
			|| !$this->_isRegistrationEnabled() 
			|| !$this->getRegistrationReport()) {

            return false;
        }

        return true;
    }

    public function getRegistrationReport() {
        $session = Mage::getSingleton('core/session', array('name' => 'frontend'));
        return $session->getIsRegistered();
    }

    protected function _toHtml() {

        $html = parent::_toHtml();

        /*  Use to remove session info set to track registration */
        if ($this->getRegistrationReport()) {
            $this->_resetRegistrationReport();
        }

        return $html;
    }

    protected function _resetRegistrationReport() {
        Mage::getSingleton('core/session')->setIsRegistered(0);
    }

}
