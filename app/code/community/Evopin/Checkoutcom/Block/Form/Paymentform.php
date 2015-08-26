<?php

class Evopin_Checkoutcom_Block_Form_Paymentform extends Mage_Payment_Block_Form_Cc
{
    function _construct()  
	{
		parent::_construct();
		$this->setTemplate('evopin/checkoutcom/form/paymentform.phtml');
	}
}
?>