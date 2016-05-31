<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */


class Amasty_Checkoutfees_Model_Options_Allpaymentmethods
{
    public function toOptionArray($isActiveOnlyFlag = false)
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $methods  = array(array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select')));

        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle          = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
            $methods[$paymentCode] = array(
                'label' => $paymentTitle,
                'value' => $paymentCode,
            );
        }

        return $methods;
    }
}