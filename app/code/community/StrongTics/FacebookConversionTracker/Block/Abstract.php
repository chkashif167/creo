<?php

/**
 * Facebook abstract block
 * 
 * @category    StrongTics
 * @package     FacebookConversionTracker
 * @author      Issa BERTHE <issaberthet@gmail.com>
 * @copyright   Copyright (c) StrongTics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class StrongTics_FacebookConversionTracker_Block_Abstract extends Mage_Core_Block_Template {

    abstract protected function _canShow();

    protected function _toHtml() {
        if (!$this->_canShow()) {
            return '';
        }
		
        return parent::_toHtml();
    }

    /**
     * Utility to get facebook js source
     * @return string
     */
    public function getFacebookScriptSrc() {
        /* @todo next release find best flexible way to manage and out Facebook js 
		return ($this->getRequest()->isSecure() ? 'https' : 'http')
                . '://connect.facebook.net/en_US/fp.js';*/
        $url = '//connect.facebook.net/en_US/fbds.js';
		return $url;
    }

    public function getFacebookOffSiteEventSrc() {
       /* @todo next release find best flexible way to manage and out Facebook event js 
	   return ($this->getRequest()->isSecure() ? 'https' : 'http')
				. '://www.facebook.com/tr';*/
        $url = 'https://www.facebook.com/tr';
		return $url;				
    }

    /**
     * returns current store currency
     * @return string
     */
    public function getStoreCurrency() {
        return $this->_getHelper()->getStoreCurrency();
    }
	
    /**
     * returns conversion Value
     * @return int
     */
    public function getConversionValue() {
        if (!$this->_getHelper()->getCartAmount()){
			return 0.01;
		}
		return number_format($this->_getHelper()->getCartAmount(),2);
    }


    /**
     * @return StrongTics_FacebookConversionTracker_Helper_Config
     */
    protected function _getConfigHelper() {
        return Mage::helper('sticsfbtracker/config');
    }

    /**
     * @return StrongTics_FacebookConversionTracker_Helper_Data
     */
    protected function _getHelper() {
        return Mage::helper('sticsfbtracker');
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

}
