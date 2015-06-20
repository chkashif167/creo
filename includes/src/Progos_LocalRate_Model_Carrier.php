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

          $result->append($this->_getNextDayRate());
          $result->append($this->_gettwoDaysRate());
          $result->append($this->_getthreeDaysRate());
          $result->append($this->_getfourDaysRate());
          return $result;
      }

     
       public function _getNextDayRate()
        {
            /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
            $rate = Mage::getModel('shipping/rate_result_method');
         
            $rate->setCarrier($this->_code);
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod('nextday');
            $rate->setMethodTitle('Delievery For Next Day');
            $rate->setPrice($this->getConfigData('price_for_one'));
            $rate->setCost(0);
         
            return $rate;
        }

        public function _gettwoDaysRate()
        {
            /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
            $rate = Mage::getModel('shipping/rate_result_method');
         
            $rate->setCarrier($this->_code);
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod('twodays');
            $rate->setMethodTitle('Delievery For Two Days');
            $rate->setPrice($this->getConfigData('price_for_two'));
            $rate->setCost(0);
         
            return $rate;
        }

       public function _getthreeDaysRate()
        {
            /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
            $rate = Mage::getModel('shipping/rate_result_method');
         
           $rate->setCarrier($this->_code);
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod('threedays');
            $rate->setMethodTitle('Delievery For Three Days');
            $rate->setPrice($this->getConfigData('price_for_three'));
            $rate->setCost(0);
         
            return $rate;
        }
         public function _getfourDaysRate()
        {
            /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
            $rate = Mage::getModel('shipping/rate_result_method');
         
           $rate->setCarrier($this->_code);
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod('fourdays');
            $rate->setMethodTitle('Delievery For Four Days');
            $rate->setPrice($this->getConfigData('price_for_four'));
            $rate->setCost(0);
         
            return $rate;
        }

   

  /**
   * This method is used when viewing / listing Shipping Methods with Codes programmatically
   */
  public function getAllowedMethods() {
    return array(
        'nextday'    =>  'Delievery For Next Day',
        'twodays'     =>  'Delievery For Two Days',
        'threedays'     =>  'Delievery For Three Days',
        'fourdays'     =>  'Delievery For Four Days',
    );
  }
}