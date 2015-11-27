<?php

/**
 * Facebook config helper
 * 
 * @category    StrongTics
 * @package     FacebookConversionTracker
 * @author      Issa BERTHE <issaberthet@gmail.com>
 * @copyright   Copyright (c) StrongTics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StrongTics_FacebookConversionTracker_Helper_Config extends Mage_Core_Helper_Abstract {

    const 	FACEBOOK_RETARGETING_CONFIG = 'sticsfbtracker/facebook_retargeting/', 
			FACEBOOK_ADDTOCART_CONFIG = 'sticsfbtracker/facebook_cart_conversion/',
            FACEBOOK_CHECKOUT_CONFIG = 'sticsfbtracker/facebook_checkout_conversion/',
            FACEBOOK_KEYPAGE_VIEW_CONFIG = 'sticsfbtracker/facebook_keypage_view_conversion/',
            FACEBOOK_LEAD_CONFIG = 'sticsfbtracker/facebook_lead_conversion/',
            FACEBOOK_OTHER_CONFIG = 'sticsfbtracker/facebook_other_conversion/',
            FACEBOOK_REGISTRATION_CONFIG = 'sticsfbtracker/facebook_registration_conversion/';

    /**
     * Check if Facebook trackers are enabled    
     * @param mixed $store
     * @return boolean
     */
    public function isRetargetingEnabled($store = null) {
        return Mage::getStoreConfigFlag(self::FACEBOOK_RETARGETING_CONFIG . 'enabled', $store);
    }
	
    public function isAddToCartEnabled($store = null) {
        return Mage::getStoreConfigFlag(self::FACEBOOK_ADDTOCART_CONFIG . 'enabled', $store);
    }

    public function isCheckoutEnabled($store = null) {
        return Mage::getStoreConfigFlag(self::FACEBOOK_CHECKOUT_CONFIG . 'enabled', $store);
    }

    public function isKeyPageViewEnabled($store = null) {
        return Mage::getStoreConfigFlag(self::FACEBOOK_KEYPAGE_VIEW_CONFIG . 'enabled', $store);
    }

    public function isLeadEnabled($store = null) {
        return Mage::getStoreConfigFlag(self::FACEBOOK_LEAD_CONFIG . 'enabled', $store);
    }

    public function isOtherEnabled($store = null) {
        return Mage::getStoreConfigFlag(self::FACEBOOK_OTHER_CONFIG . 'enabled', $store);
    }

    public function isRegistrationEnabled($store = null) {
        return Mage::getStoreConfigFlag(self::FACEBOOK_REGISTRATION_CONFIG . 'enabled', $store);
    }

    /**
     * Returns Facebook trackers pixels Id    
     * @param mixed $store
     * @return int
     */
    public function getRetargetingPixelId($store = null) {
        return Mage::getStoreConfig(self::FACEBOOK_RETARGETING_CONFIG . 'retargeting_pixel_id', $store);
    }
	
    public function getAddToCartConversionPixelId($store = null) {
        return Mage::getStoreConfig(self::FACEBOOK_ADDTOCART_CONFIG . 'cart_pixel_id', $store);
    }

    public function getCheckoutConversionPixelId($store = null) {
        return Mage::getStoreConfig(self::FACEBOOK_CHECKOUT_CONFIG . 'checkout_pixel_id', $store);
    }

    public function getKeyPageViewConversionPixelId($store = null) {
        return Mage::getStoreConfig(self::FACEBOOK_KEYPAGE_VIEW_CONFIG . 'key_page_pixel_id', $store);
    }

    public function getLeadConversionPixelId($store = null) {
        return Mage::getStoreConfig(self::FACEBOOK_LEAD_CONFIG . 'lead_pixel_id', $store);
    }

    public function getOtherWebsiteConversionPixelId($store = null) {
        return Mage::getStoreConfig(self::FACEBOOK_OTHER_CONFIG . 'other_pixel_id', $store);
    }

    public function getRegistrationConversionPixelId($store = null) {
        return Mage::getStoreConfig(self::FACEBOOK_REGISTRATION_CONFIG . 'registration_pixel_id', $store);
    }

    /**
     * Returns Facebook tracker pixels selected page    
     * @param mixed $store
     * @return int
     */
    public function getKeyPageView($store = null) {
        return Mage::getStoreConfig(self::FACEBOOK_KEYPAGE_VIEW_CONFIG . 'key_page', $store);
    }

    public function getOtherPage($store = null) {
        return Mage::getStoreConfig(self::FACEBOOK_OTHER_WEBSITE_CONFIG . 'other_page', $store);
    }

}
