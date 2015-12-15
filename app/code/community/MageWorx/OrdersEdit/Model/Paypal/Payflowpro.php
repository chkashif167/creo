<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Paypal_Payflowpro extends MageWorx_OrdersEdit_Model_Paypal_Payflowpro_Abstract
{
    /**
     * @return $this|Mage_Payment_Model_Abstract
     */
    public function validate() {
        if ($this->isEditOrderWithSaveCart()) {
            return $this;
        }
        return parent::validate();
    }

    /**
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this|Mage_Paypal_Model_Payflowpro
     */
    public function authorize(Varien_Object $payment, $amount) {
    	if ($this->isEditOrderWithSaveCart()) {
            return $this;
        }
    	return parent::authorize($payment, $amount);	
    }

    /**
     * @param Varien_Object $payment
     * @return $this|Mage_Paypal_Model_Payflowpro
     */
    public function cancel(Varien_Object $payment) {        
        if ($this->isEditOrderWithSaveCart()) {
            return $this;
        }
        return parent::cancel($payment);
    }

    /**
     * @return bool
     */
    public function isEditOrderWithSaveCart() {
        if (Mage::app()->getRequest()->getControllerName()=='ordersedit_order_edit') {
            $paymentData = Mage::app()->getRequest()->getPost('payment');
            // if method=='verisign' - must be card number to validate payment data 
            if ($paymentData && isset($paymentData['method']) && $paymentData['method']=='verisign' && !isset($paymentData['cc_number'])) {
                return true;
            }
        }
        return false;
    }   
}