<?php

/**
 * Facebook Key Page Views Conversion Pixel
 * 
 * @category    StrongTics
 * @package     FacebookConversionTracker
 * @author      Issa BERTHE <issaberthet@gmail.com>
 * @copyright   Copyright (c) StrongTics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StrongTics_FacebookConversionTracker_Block_Keypage extends StrongTics_FacebookConversionTracker_Block_Abstract {

    protected function _isKeyPageViewEnabled() {
        return $this->_getConfigHelper()->isKeyPageViewEnabled();
    }

    public function getKeyPageViewConversionPixelId() {
        return $this->_getConfigHelper()->getKeyPageViewConversionPixelId();
    }

    protected function _canShow() {
        if (!$this->getKeyPageViewConversionPixelId() 
			|| !$this->_isKeyPageViewEnabled() 
			|| $this->_currentPage() != $this->_getConfigHelper()->getKeyPageView()) {

            return false;
        }

        return true;
    }

    protected function _currentPage() {
        return Mage::getSingleton('cms/page')->getIdentifier();
    }

}
