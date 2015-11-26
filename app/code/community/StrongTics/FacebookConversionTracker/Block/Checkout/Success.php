<?php

/**
 * Facebook Checkout Conversion Pixel
 * 
 * @category    StrongTics
 * @package     FacebookConversionTracker
 * @author      Issa BERTHE <issaberthet@gmail.com>
 * @copyright   Copyright (c) StrongTics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StrongTics_FacebookConversionTracker_Block_Checkout_Success extends StrongTics_FacebookConversionTracker_Block_Abstract {

    public function getCheckoutConversionPixelId() {
        return $this->_getConfigHelper()->getCheckoutConversionPixelId();
    }

    protected function _canShow() {

        if (!$this->getCheckoutConversionPixelId() || !$this->_getConfigHelper()->isCheckoutEnabled()) {
            return false;
        }

        return true;
    }

    public function getOrderAmount() {
        return number_format($this->_getOrder()->getGrandTotal(),2);
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder() {
        return Mage::helper('sticsfbtracker')->getLastOrder();
    }

}
