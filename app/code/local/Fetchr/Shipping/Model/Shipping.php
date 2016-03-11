<?php
/**
 * @category   Fetchr
 * @package    Fetchr_Shipping
 * @author     Islam Khalil
 * @website    Fetchr.us
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
    */
class Fetchr_Shipping_Model_Shipping extends Mage_Shipping_Model_Shipping
{
    public function collectCarrierRates($carrierCode, $request)
    {
        if (!$this->_checkCarrierAvailability($carrierCode, $request)) {
            return $this;
        }
        return parent::collectCarrierRates($carrierCode, $request);
    }
 
    protected function _checkCarrierAvailability($carrierCode, $request = null)
    {
        $showInFronend  = Mage::getStoreConfig('carriers/fetchr/showinfrontend');
        if(!$showInFronend){
            if($carrierCode == 'fetchr'){ #Hide Flat Rate for non logged in customers
                return false;
            }
        }
        return true;
    }
}