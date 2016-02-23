<?php
class Tentura_Ngroups_Model_Observer{
    
    public function setSubscriberStore($observer){
        
        if($storeId = $observer->getDataObject()->getNewStoreId()){
            $observer->getDataObject()->setStoreId($storeId);
        }
        
    }
    
}