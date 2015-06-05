<?php
 
/**
* Our test shipping method module adapter
*/
class Progos_LocalRate_Model_Carrier_ShippingMethod extends Mage_Shipping_Model_Carrier_Abstract
{
  /**
   * unique internal shipping method identifier
   *
   * @var string [a-z0-9_]
   */
  protected $_code = 'localrate';
 
  /**
   * Collect rates for this shipping method based on information in $request
   *
   * @param Mage_Shipping_Model_Rate_Request $data
   * @return Mage_Shipping_Model_Rate_Result
   */
  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
    // skip if not enabled
    if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
        return false;
    }
 
    /**
     * here we are retrieving shipping rates from external service
     * or using internal logic to calculate the rate from $request
     * you can see an example in Mage_Usa_Model_Shipping_Carrier_Ups::setRequest()
     */
 
    // get necessary configuration values
    $handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
 
    // this object will be returned as result of this method
    // containing all the shipping rates of this method
    $result = Mage::getModel('shipping/rate_result');
 
    // $response is an array that we have
    foreach ($response as $rMethod) {
      // create new instance of method rate
      $method = Mage::getModel('shipping/rate_result_method');
 
      // record carrier information
      $method->setCarrier($this->_code);
      $method->setCarrierTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/title'));
 
      // record method information
      $method->setMethod($rMethod['code']);
      $method->setMethodTitle($rMethod['title']);
 
      // rate cost is optional property to record how much it costs to vendor to ship
      $method->setCost($rMethod['amount']);
 
      // in our example handling is fixed amount that is added to cost
      // to receive price the customer will pay for shipping method.
      // it could be as well percentage:
      /// $method->setPrice($rMethod['amount']*$handling/100);
      $method->setPrice($rMethod['amount']+$handling);
 
      // add this rate to the result
      $result->append($method);
    }
 
    return $result;
  }
 
  /**
   * This method is used when viewing / listing Shipping Methods with Codes programmatically
   */
  public function getAllowedMethods() {
    return array($this->_code => $this->getConfigData('name'));
  }
}