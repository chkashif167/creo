<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */


class Amasty_Checkoutfees_Block_Checkout_Total_Amcheckoutfees extends Mage_Checkout_Block_Total_Default
{
    protected $_template = 'amasty/amcheckoutfees/checkout/total/amcheckoutfees.phtml';

    public function __construct()
    {
        parent::_construct();
        $fees       = array();
        $quote      = Mage::getSingleton('checkout/session')->getQuote();
        $currency = $quote->getOrderCurrencyCode() ? $quote->getOrderCurrencyCode() : $quote->getQuoteCurrencyCode();
        $storedData = $quote->getAmcheckoutfeesFees();

        if (!Mage::getStoreConfig('amcheckoutfees/general/enabled')) {
            $quote->setAmcheckoutfeesFees(array());
            $quote->save();
            $quote->setTotalsCollectedFlag(false)->collectTotals();
            $this->setFeesData(false);
            $this->setCurrency($currency);
            $storedData = false;

            return false;
        }

        if ($storedData) {
            $storedData = unserialize($storedData);
            foreach ($storedData as $storedFeesId => $storedFeesData) {
                $storedFeeOptions = Mage::getModel('amcheckoutfees/feesData')
                                        ->getCollection()
                                        ->addFieldToFilter('fees_data_id', array('in' => explode(',', $storedFeesData)))
                                        ->setOrder('sort', 'ASC')
                                        ->getItems();
                $storedFeeData    = Mage::getModel('amcheckoutfees/fees')
                                        ->load($storedFeesId);
                if ($storedFeeOptions) {
                    // save all fee data for render
                    $fees[$storedFeesId] = array(
                        'fee'     => $storedFeeData,
                        'options' => $storedFeeOptions,
                    );
                }
            }
            $this->setFeesData($fees);
            $this->setCurrency($currency);
        }
    }
}
