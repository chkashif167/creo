<?php
class FacileCheckout_OnestepCheckout_Helper_Url extends Mage_Checkout_Helper_Url
{
    public function getCheckoutUrl()
    {
        return $this->_getUrl('onestepcheckout', array('_secure'=>true));
    }
}
