<?php
class FacileCheckout_OnestepCheckout_Block_Widget_Dob extends Mage_Customer_Block_Widget_Dob
{
    protected $_dateInputs = array();
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('onestepcheckout/widget/dob.phtml');
    }
}
