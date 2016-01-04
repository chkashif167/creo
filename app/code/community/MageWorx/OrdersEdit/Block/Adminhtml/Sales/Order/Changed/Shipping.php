<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Changed_Shipping extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /** @var string  */
    protected $_template = 'mageworx/ordersedit/changed/shipping.phtml';

    /**
     * @return mixed
     */
    public function getActiveMethodRate()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $this->getQuote();
        /** @var array $rates */
        $rates = $quote->getShippingAddress()->getGroupedAllShippingRates();
        /** @var string $method */
        $method = $quote->getShippingAddress()->getShippingMethod();

        if (is_array($rates)) {
            foreach ($rates as $group) {
                foreach ($group as $code => $rate) {
                    if ($rate->getCode() == $method) {
                        return $rate;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param $carrierCode
     * @return string
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title', $this->getOrder()->getStoreId())) {
            return $name;
        }
        return $carrierCode;
    }

    /**
     * @return mixed
     */
    public function getShippingPrice()
    {
        return $this->getQuote()->getStore()->convertPrice(
            $this->getQuote()->getShippingAddress()->getBaseShippingAmount(),
            true
        );
    }
}