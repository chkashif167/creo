<?php

/**
 * Facebook Adds to Cart Conversion Pixel
 * 
 * @category    StrongTics
 * @package     FacebookConversionTracker
 * @author      Issa BERTHE <issaberthet@gmail.com>
 * @copyright   Copyright (c) StrongTics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StrongTics_FacebookConversionTracker_Block_Checkout_Cart extends StrongTics_FacebookConversionTracker_Block_Abstract {

    protected function _isAddToCartEnabled() {
        return $this->_getConfigHelper()->isAddToCartEnabled();
    }

    public function getAddToCartConversionPixelId() {
        return $this->_getConfigHelper()->getAddToCartConversionPixelId();
    }

    protected function _canShow() {
        if (!$this->getAddToCartConversionPixelId() || !$this->_isAddToCartEnabled()) {
            return false;
        }
		
        return true;
    }

}
