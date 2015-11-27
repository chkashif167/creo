<?php

/**
 * Default data helper on module
 * 
 * @category    StrongTics
 * @package     FacebookConversionTracker
 * @author      Issa BERTHE <issa.berthe@strongtics.Com>
 * @copyright   Copyright (c) StrongTics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StrongTics_FacebookConversionTracker_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Get the last order from checkout session
     * @return Mage_Sales_Model_Order|null
     */
    public function getLastOrder() {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            return $order;
        }

        return null;
    }

    /**
     * Returns cart GrandTotal
	 * @return int|null
     */	
    public function getCartAmount() {
		$cartAmount = Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();
		if($cartAmount){
			return $cartAmount;
		}
		
		return null;
    }	

    /**
     * Utility to get store currency
     * @return string
     */
    public function getStoreCurrency($store = null) {
        return Mage::app()->getStore($store)->getCurrentCurrencyCode();
    }

}
