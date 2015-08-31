<?php

class FacileCheckout_OnestepCheckout_Block_Onepage_Link extends Mage_Core_Block_Template
{
    public function isOnestepCheckoutAllowed()
    {
        return $this->helper('onestepcheckout')->isOnestepCheckoutEnabled();
    }

    public function checkEnable()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount();
    }

    public function getOnestepCheckoutUrl()
    {
    	$url	= $this->getUrl('onestepcheckout', array('_secure' => true));
        return $url;
    }
}
