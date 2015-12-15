<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Sales_Order_Payment extends MageWorx_OrdersEdit_Model_Sales_Order_Payment_Abstract
{
    /**
     * @return $this|Mage_Sales_Model_Order_Payment
     */
   public function cancel() {
       $paymentData = Mage::app()->getRequest()->getPost('payment');
       // not to cancel previos transaction 
       if ($paymentData && isset($paymentData['method']) && ($paymentData['method']=='authorizenet' || $paymentData['method']=='authorizenet_directpost') && !isset($paymentData['cc_number'])) {
           return $this;
       }
       return parent::cancel();
   }
}
