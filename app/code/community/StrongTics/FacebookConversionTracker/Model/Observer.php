<?php

/**
 * Maxiscoot_Tag_Model_Observer
 * 
 * @category    StrongTics
 * @package     FacebookConversionTracker
 * @author      Issa BERTHE <issa.berthe@strongtics.Com>
 * @copyright   Copyright (c) StrongTics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StrongTics_FacebookConversionTracker_Model_Observer {

    /**
     * Use to trigger cunstomer register    
     * @return void|Varien_Event_Observer
     */
    public function trackCustomerRegister() {

        Mage::getSingleton('core/session')->setIsRegistered('1');
    }

    /**
     * Use to process after contact page submit    
     * @return void|Varien_Event_Observer   
     */
    public function afterContactSubmitObserver() {

        Mage::getSingleton('core/session')->setIsContactSubmit('1');
    }

}
