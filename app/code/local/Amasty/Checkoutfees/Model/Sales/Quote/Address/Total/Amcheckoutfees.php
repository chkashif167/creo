<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */


class Amasty_Checkoutfees_Model_Sales_Quote_Address_Total_Amcheckoutfees extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    protected $_code = 'amcheckoutfees';

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);

        /*
         * check if module enabled
        */
        if (!Mage::getStoreConfig('amcheckoutfees/general/enabled')) {
            $this->_setBaseAmount(0);
            $this->_setAmount(0);

            return false;
        }

        /*
         * check if only address type shipping coming through
         */
        $items = $this->_getAddressItems($address);
        if (!count($items) || Mage::registry('amcheckoutfees_quote_processing')) {
            return $this;
        }

        /*
         * refresh variables
         */
        Mage::register('amcheckoutfees_quote_processing', 1);
        $this->_setAmount(0);
        $this->_setBaseAmount(0);
        $amount = 0;
        $base = 0;

        $quote      = Mage::getSingleton('checkout/session')->getQuote();
        $storedData = $quote->getAmcheckoutfeesFees();

        if ($storedData) {
            $storedData = unserialize($storedData);
            foreach ($storedData as $storedFeesId => $storedFeesData) {
                $storedFeeData = Mage::getModel('amcheckoutfees/feesData')
                                     ->getCollection()
                                     ->addFieldToFilter('fees_data_id', array('in' => explode(',', $storedFeesData)));
                if ($storedFeeData->getSize()) {
                    foreach ($storedFeeData as $fee) {
                        if ($fee->getPriceType()) {
                            $amount += $fee->getPrice() * ($address->getSubtotal() * 0.01);
                            $base += $fee->getPrice() * ($address->getBaseSubtotal() * 0.01);
                        } else {
                            $amount += Mage::helper('core')->currency($fee->getPrice(), false, false);
                            $base += $fee->getPrice();
                        }
                    }
                }
            }
        }

        if ($amount) {
            $this->_setBaseAmount($base);
            $this->_setAmount($amount);
        }

        Mage::unregister('amcheckoutfees_quote_processing');

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        /*
         * check if module enabled
        */
        if (!Mage::getStoreConfig('amcheckoutfees/general/enabled')) {
            return $this;
        }

        $amcheckoutfeesAmount = $address->getAmcheckoutfeesAmount();
        if ($amcheckoutfeesAmount) {
            $address->addTotal(
                array(
                    "code"  => $this->getCode(),
                    "title" => Mage::helper('amcheckoutfees')->__("Checkout Fees"),
                    "value" => $amcheckoutfeesAmount
                )
            );
        }

        return $this;
    }
}
