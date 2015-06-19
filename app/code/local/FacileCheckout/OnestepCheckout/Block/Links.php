<?php
class FacileCheckout_OnestepCheckout_Block_Links extends Mage_Checkout_Block_Links
{
    public function addCheckoutLink()
    {
		if ($this->helper('onestepcheckout')->isOnestepCheckoutEnabled()) 
        {
        	$parent = $this->getParentBlock();
			if ($parent)
            	$parent->addLink($this->helper('checkout')->__('Checkout'), 'onestepcheckout', $this->helper('checkout')->__('Checkout'), true, array('_secure'=> true), 60, null, 'class="top-link-checkout"');

			return $this;
        }
        else
            return parent::addCheckoutLink();
    }
}