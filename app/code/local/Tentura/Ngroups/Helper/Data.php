<?php
class Tentura_Ngroups_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_SHOW_FORM_CONFIG = "newsletter/ngroups/show_block";
    const XML_SHOW_NAME_CONFIG = "newsletter/ngroups/show_name";
    const XML_SHOW_PHONE_CONFIG = "newsletter/ngroups/show_phone";
    const XML_ENABLE_MULTYPLY_CONFIG = "newsletter/ngroups/multiply";
    const XML_ENABLE_ALLOW_MANAGE = "newsletter/ngroups/allow_manage";
    

    public function isEnabledForFrontend(){
        
        return Mage::getStoreConfig(self::XML_SHOW_FORM_CONFIG);
        
    }
    public function isManagementAllowed(){
        
        return Mage::getStoreConfig(self::XML_ENABLE_ALLOW_MANAGE);
        
    }
    public function isShowName(){
        
        //if (Mage::helper('customer')->isLoggedIn()){
        //    return false;
      //  }
        return Mage::getStoreConfig(self::XML_SHOW_NAME_CONFIG);
        
    }
    public function isShowPhone(){

     //   if (Mage::helper('customer')->isLoggedIn()){
      //      return false;
      //  }
        return Mage::getStoreConfig(self::XML_SHOW_PHONE_CONFIG);
        
    }
    public function isAllowMultiply(){
        
        return Mage::getStoreConfig(self::XML_ENABLE_MULTYPLY_CONFIG);
        
    }

    public function getGroupsList(){
        
        $groups = Mage::getModel("customer/group")->getCollection();
        $list = array();
        foreach ($groups as $group){
            
            $list[$group->getData("customer_group_id")]  = $group->getData("customer_group_code");
                    
        }
        
        return $list;
        
    }
    /*
     * string - comma separated customers
     */
    public function subscribersToArray($string){
        
        if (!$string){
            return array();
        }
        
        $subscribers = explode(',', $string);
        // Delete empty data from array
        foreach ($subscribers as $key => $subscriber) {
            if (!$subscriber) {
                unset($subscribers[$key]);
            }
        }
        return $subscribers;
        
    }
    
    public function getSubscribersAsArray($collection){
        
        $list = array();
        foreach($collection as $groupSubscriber){
            $list[] = $groupSubscriber->getSubscriberId();
        }  
        return $list;
            
    }
    public function getSubscriberIdByEmail($email){
        return Mage::getModel("newsletter/subscriber")->loadByEmail($email)->getId();
    }
    
    public function getStoresNumber($flag = false){
        $i = 0;
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                $quantity = count($stores);
                if($flag){
                    foreach ($stores as $store) {
                        if($quantity == 1){
                            $store_id = $store->getId();
                            return $store_id;
                        }
                    }
                }
            }
        }
        
        return $quantity;
    }
    
}