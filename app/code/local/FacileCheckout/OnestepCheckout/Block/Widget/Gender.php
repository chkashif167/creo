<?php
class FacileCheckout_OnestepCheckout_Block_Widget_Gender extends Mage_Customer_Block_Widget_Gender
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('onestepcheckout/widget/gender.phtml');
    }
}
