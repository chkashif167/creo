<?php

/**
 * Facebook Other (Page) Conversion Pixel Block
 * 
 * @category    StrongTics
 * @package     FacebookConversionTracker
 * @author      Issa BERTHE <issaberthet@gmail.com>
 * @copyright   Copyright (c) StrongTics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StrongTics_FacebookConversionTracker_Block_Other extends StrongTics_FacebookConversionTracker_Block_Abstract {

    protected function _isOtherEnabled() {
        return $this->_getConfigHelper()->isOtherEnabled();
    }

    public function getOtherWebsiteConversionPixelId() {
        return $this->_getConfigHelper()->getOtherWebsiteConversionPixelId();
    }

    protected function _canShow() {
        if (!$this->_isOtherEnabled() 
			|| !$this->getOtherWebsiteConversionPixelId() 
			|| $this->_currentPage() != $this->_getConfigHelper()->isOtherEnabled()) {

            return false;
        }

        return true;
    }

    protected function _currentPage() {
        return Mage::getSingleton('cms/page')->getIdentifier();
    }

}
