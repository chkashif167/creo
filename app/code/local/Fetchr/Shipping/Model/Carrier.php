<?php
/**
 * Fetchr
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * https://fetchr.zendesk.com/hc/en-us/categories/200522821-Downloads
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to ws@fetchr.us so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Fetchr Magento Extension to newer
 * versions in the future. If you wish to customize Fetchr Magento Extension (Fetchr Shipping) for your
 * needs please refer to http://www.fetchr.us for more information.
 *
 * @author     Islam Khalil
 * @package    Fetchr Shipping
 * Used in creating options for fulfilment|delivery config value selection
 * @copyright  Copyright (c) 2015 Fetchr (http://www.fetchr.us)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Fetchr_Shipping_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
  protected $_code = 'fetchr';
 
  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
    if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
        return false;
    }
    $handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
    $result   = Mage::getModel('shipping/rate_result');
    $method   = Mage::getModel('shipping/rate_result_method');
    
    $allowedMethods = $this->getAllowedMethods();
    $allowedMethods = explode(',', $allowedMethods);
    
    if(count($allowedMethods) == 1){

      $methodName = $allowedMethods[0];
      if($methodName == 'next_day'){
        $result->append($this->_getStandardRate());  
      }else{
        $result->append($this->_getExpressRate());
      }

    }else{
      $result->append($this->_getStandardRate());
      $result->append($this->_getExpressRate());
    }
 
    return $result;
  }
 
  public function getAllowedMethods()
  {

    return $this->getConfigData('shippingoption');   
  }
 
  protected function _getStandardRate()
  {
    $rate = Mage::getModel('shipping/rate_result_method');
     
    $rate->setCarrier($this->_code);
    $rate->setCarrierTitle($this->getConfigData('title'));
    $rate->setMethod('next_day');
    $rate->setMethodTitle('Next Day Delivery');
    $rate->setPrice($this->getConfigData('nextdaydeliveryrate'));
    $rate->setCost('0');
     
    return $rate;
  }

  protected function _getExpressRate()
  {
    $rate = Mage::getModel('shipping/rate_result_method');
     
    $rate->setCarrier($this->_code);
    $rate->setCarrierTitle($this->getConfigData('title'));
    $rate->setMethod('same_day');
    $rate->setMethodTitle('Same Day Delivery');
    $rate->setPrice($this->getConfigData('samedaydeliveryrate'));
    $rate->setCost('0');
     
    return $rate;
  }

  public function isTrackingAvailable()
  {
      return true;
  }

  public function getTrackingInfo($tracking)
  {
      $track = Mage::getModel('shipping/tracking_result_status');
      $track->setUrl('http://track.menavip.com/track.php?tracking_number=' . $tracking)
          ->setTracking($tracking)
          ->setCarrierTitle($this->getConfigData('name'));
      return $track;
  }
}