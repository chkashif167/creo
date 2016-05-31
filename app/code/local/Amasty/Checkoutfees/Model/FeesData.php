<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Model_FeesData extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('amcheckoutfees/feesData');
    }

    public function getTitleForStore()
    {
        $store = Mage::app()->getStore()->getId();
        $title = $this->getData('title') ? unserialize($this->getData('title')) : array();
        if (isset($title[$store]) && !empty($title[$store])) {
            $title = $title[$store];
        } else if (isset($title[0]) && !empty($title[0])) {
            $title = $title[0];
        } else {
            $title = '';
        }

        return $title;
    }

    public function getFullPrice($order = false, $currency = 'USD')
    {
        $defaultCurrency = Mage::app()->getStore()->getBaseCurrencyCode();
        $total           = Mage::getSingleton('checkout/session')->getQuote()->getBaseSubtotal();
        if ($order !== false) {
            $total = $order->getBaseSubtotal();
        }
        if ($total && $this->getPriceType()) {
            $total = $this->getPrice() * 0.01 * $total;
        } else {
            $total = $this->getPrice();
        }

        $total = Mage::helper('directory')->currencyConvert($total, $defaultCurrency, $currency);

        return $total;
    }

}