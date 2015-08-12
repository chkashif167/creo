<?php
 
/**
* Our test shipping method module adapter
*/
class Progos_InternationalRate_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
  /**
   * unique internal shipping method identifier
   *
   * @var string [a-z0-9_]
   */

  protected $_code = 'progos_internationalrate';
 
  /**
   * Collect rates for this shipping method based on information in $request
   *
   * @param Mage_Shipping_Model_Rate_Request $data
   * @return Mage_Shipping_Model_Rate_Result
   */
     public function collectRates(Mage_Shipping_Model_Rate_Request $request)
      {
          $result = Mage::getModel('shipping/rate_result');

          $result->append($this->_getExpressRate());
          $result->append($this->_getStandardRate());
          return $result;
      }

     
       public function _getExpressRate()
        {
            /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
            $rate = Mage::getModel('shipping/rate_result_method');
         
            $rate->setCarrier($this->_code);
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod('express');
            $rate->setMethodTitle('Express Rate');
            $rate->setPrice($this->getConfigData('price_for_express'));
            $rate->setCost(0);
         
            return $rate;
        }

        public function _getStandardRate()
        {
            /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
            $rate = Mage::getModel('shipping/rate_result_method');
         
            $rate->setCarrier($this->_code);
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod('standard');
            $rate->setMethodTitle('Standard Rate');
            $rate->setPrice($this->getConfigData('price_for_standard'));
            $rate->setCost(0);
         
            return $rate;
        }

   
  /**
   * This method is used when viewing / listing Shipping Methods with Codes programmatically
   */
  public function getAllowedMethods() {
    return array(
        'express'    =>  'Express Rate',
        'standard'     =>  'Standard Rate',
        
    );
  }
}