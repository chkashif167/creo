<?php
 
/**
* Our test shipping method module adapter
*/
class Progos_LocalRate_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
  /**
   * unique internal shipping method identifier
   *
   * @var string [a-z0-9_]
   */

  protected $_code = 'progos_localrate';
 
  /**
   * Collect rates for this shipping method based on information in $request
   *
   * @param Mage_Shipping_Model_Rate_Request $data
   * @return Mage_Shipping_Model_Rate_Result
   */
   public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $result = Mage::getModel('shipping/rate_result');
 
          foreach ($request->getAllItems() as $item) {
          $_product= Mage::getSingleton('catalog/product')->load($item->getProductId());
          $_customOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
          foreach($_customOptions['attributes_info'] as $_option){ 
            if($_option['value'] == 'nextday')
            {
                $next_day = $this->getConfigData('price_for_one') * $item->getQty();
            }
            else if($_option['value'] == '2 days')
            {
                $two_days = $this->getConfigData('price_for_one') * $item->getQty();
            }
            else if($_option['value'] == '3 days')
            {
                $three_days = $this->getConfigData('price_for_one') * $item->getQty();
            }
            else if($_option['value'] == '4 days')
            {
                $four_days = $this->getConfigData('price_for_one') * $item->getQty();
            }
          }
        }

             $amount = floatval($nextday + $two_days + $three_days + $four_days);
        
             $result->append($this->_getStandardRate($amount));
         
         
          return $result;
    }

    public function _getStandardRate($amount)
    {
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');
     
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('large');
        $rate->setMethodTitle('Standard delivery');
        $rate->setPrice($amount);
        $rate->setCost(0);
     
        return $rate;
    }

  /**
   * This method is used when viewing / listing Shipping Methods with Codes programmatically
   */
  public function getAllowedMethods() {
    return array(
        'standard'    =>  'Standard delivery',
        'express'     =>  'Express delivery',
    );
  }
}