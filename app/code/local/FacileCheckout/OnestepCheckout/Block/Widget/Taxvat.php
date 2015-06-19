<?php
class FacileCheckout_OnestepCheckout_Block_Widget_Taxvat extends Mage_Customer_Block_Widget_Taxvat
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('onestepcheckout/widget/taxvat.phtml');
    }
}
