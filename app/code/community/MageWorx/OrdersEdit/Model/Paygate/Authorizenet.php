<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Paygate_Authorizenet extends MageWorx_OrdersEdit_Model_Paygate_Authorizenet_Abstract
{
    /**
     * @return $this|Mage_Payment_Model_Abstract
     */
    public function validate() {        
        if (Mage::app()->getRequest()->getControllerName()=='ordersedit_order_edit') {
            $paymentData = Mage::app()->getRequest()->getPost('payment');
            // if method=='ccsave' - must be card number to validate payment data 
            if ($paymentData && isset($paymentData['method']) && $paymentData['method']=='authorizenet' && !isset($paymentData['cc_number'])) {
                return $this;
            }
        }        
        return parent::validate();
    }

    /**
     * @param Mage_Payment_Model_Info $payment
     * @param decimal $amount
     * @param string $requestType
     * @return $this|Mage_Paygate_Model_Authorizenet
     */
    public function _place($payment, $amount, $requestType) {
        if (Mage::app()->getRequest()->getControllerName()=='ordersedit_order_edit') {
            $paymentData = Mage::app()->getRequest()->getPost('payment');
            // if method=='ccsave' - must be card number to validate payment data 
            if ($paymentData && isset($paymentData['method']) && $paymentData['method']=='authorizenet' && !isset($paymentData['cc_number'])) {
                return $this;
            }
        } 
        return parent::_place($payment, $amount, $requestType);
    }

    /**
     * @return string
     */
    public function getConfigPaymentAction() {
    	if (Mage::app()->getRequest()->getControllerName()=='ordersedit_order_edit') {
            $paymentData = Mage::app()->getRequest()->getPost('payment');
            // if method=='ccsave' - must be card number to validate payment data 
            if ($paymentData && isset($paymentData['method']) && $paymentData['method']=='authorizenet' && !isset($paymentData['cc_number'])) {
                return '';
            }
        } 
    	return parent::getConfigPaymentAction();
    }
    
}
