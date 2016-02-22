<?php

class Tentura_Ngroups_Model_Ngroups extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ngroups/ngroups');
    }
    public function getGroupSubscribers($groupId, $count = false, $store_ids = NULL){
        
        
        $subscribers = Mage::getModel('ngroups/ngroupitems')->getCollection()->addFieldToFilter("group_id", $groupId);
        
        if($store_ids) {
            
            $iterator = 0;
            $string_query = '';
            foreach($store_ids as $key => $store_id) {
                if($iterator == 0) {
                    $operator = '';
                }else {
                    $operator = 'OR';
                }
                $string_query .= $operator . ' store_id = ' . $store_id . ' ';
                $iterator++;
            }
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $subscribersInNgroup = $readConnection->fetchAll("SELECT subscriber_id, group_id, count(subscriber_id) 
                                                     FROM ".$resource->getTableName('ngroups_items')."
                                                     WHERE subscriber_id  IN 
                                                         (
                                                             SELECT subscriber_id 
                                                             FROM ".$resource->getTableName('newsletter_subscriber')."
                                                             WHERE ". $string_query."                                                        
                                                         ) AND group_id = ".$groupId. "  
                                                     ");   
            if($count) {
                return $subscribersInNgroup[0]["count(subscriber_id)"];
            }
            return $subscribersInNgroup;
        }
        if ($count){
            return sizeof($subscribers);
        }
        return $subscribers;
        
    }
    
     public function getGroupVisibleSubscribers($groupId, $count = false){
         
        $list = array();
        $subscribersCollection = array();
        $subscriberIds = array();
        $subscribers = array();
        $hasGuest = false;
        $customerEntities = Mage::getModel('customer/customer')->getCollection()->getData(); //all customers
        $ngroupSubscribers = Mage::getModel('ngroups/ngroupitems')->getCollection()->addFieldToFilter("group_id", $groupId); //all ngroup items
        $newsletterGroup = Mage::getModel("ngroups/ngroups")->load($groupId); //getting newsletter group model
        $customerGroups = $newsletterGroup->getCustomerGroups();  //getting customers groups     
        
        if($customerGroups != ''){
            $customerGroups = explode(",",$customerGroups);  //put groups into array
        }else{
            $customerGroups = NULL;
        }
        
        foreach($ngroupSubscribers as $subscriber){
            $subscribers[] = $subscriber->getSubscriberId(); //taking items ids #1
        }
        
        if ($count){
            return sizeof($subscribers);
        }
        
        if($newsletterGroup->getStoreIds()){
            $store_ids = $newsletterGroup->getStoreIds();
            $store_ids = explode(',', $store_ids);
            //$subscribersInGroupCollection = Mage::getModel("ngroups/ngroups")->getGroupSubscribers($groupId, NULL, $store_ids);
            foreach($store_ids as $key => $store_id) {
                    $subscribersCollection = array_merge($subscribersCollection, Mage::getModel("newsletter/subscriber")->getCollection()
                            ->addFieldToFilter('store_id', $store_id)
                            ->getData());
            }
        }else{
            $subscribersCollection = array_merge($subscribersCollection, Mage::getModel("newsletter/subscriber")->getCollection()->getData());
        }
     
        foreach ($subscribersCollection as $key => $value){
             $subscriberIds[] = $value['subscriber_id'];
        }
        $subscribers = array_intersect($subscribers, $subscriberIds);
         
        if($customerGroups) {
            foreach ($customerGroups as $cGroup){
                 $UserGroupCustomers = $customerEntities = Mage::getModel('customer/customer')
                                ->getCollection()
                                ->addAttributeToFilter('group_id', $cGroup)
                                //->addAttributeToFilter('store_id', $store_id)
                                ->getData();    
                    //// get alls customers with current group_id in iteration
                    foreach($UserGroupCustomers as $customersInUserGroup){
                        ///////////// take customer in certain user group
                        foreach ($subscribersCollection as $subscriberIndex=>$subscriber) {
                            if ($subscriber['customer_id'] == $customersInUserGroup['entity_id']){   //// if customer id and subscriber are match fill the array
                                $groupSubscribers[$subscriber['subscriber_id']]['group_id'] = $customersInUserGroup['group_id'];
                                $groupSubscribers[$subscriber['subscriber_id']]['customer_id'] = $customersInUserGroup['entity_id'];
                                $groupSubscribers[$subscriber['subscriber_id']]['subscriber_email'] = $customersInUserGroup['email'];
                                $list[] =$subscriber['subscriber_id'];
                            }
                        }
                    }

                if($cGroup == 0){
                    $hasGuest = true;   
                } 
            }
        
            if (!$hasGuest){
                   //$string = $subscribers.$string;
            }else {
                foreach ($subscribersCollection as $subscriberIndex=>$subscriber) {
                    if ($subscriber['customer_id'] == 0) {                    /// if subscriber is only subscriber fill array
                         $groupSubscribers[$subscriber['subscriber_id']]['group_id'] = 0;
                         $groupSubscribers[$subscriber['subscriber_id']]['customer_id'] = 0;
                         $groupSubscribers[$subscriber['subscriber_id']]['subscriber_email'] = $subscriber['subscriber_email'];
                         $list[] =$subscriber['subscriber_id'];
                     }
                }
            }
        }
              
        $i = 0;
        if(isset($groupSubscribers)) {
            if(isset($subscribers)) { 
                foreach ($subscribers as $subscriberIndex => $subscriberId) {
                    foreach ($groupSubscribers as $groupSubscriberId=>$groupSubscriberData) {
                        if ($groupSubscriberId == $subscriberId){
                            unset($subscribers[$i]);
                        }   
                    }

                    $i++;
                }

                $list = array_merge($list, $subscribers);
            }
        } else {
            $list = $subscribers;
        }
        
        return $list;
    }
    
    
    
    
    public function getSubscriberGroups($subscriberId, $count = false, $collection = false){
        
        $groups = Mage::getModel('ngroups/ngroupitems')->getCollection()->addFieldToFilter("subscriber_id", $subscriberId);
        if ($count){
            return sizeof($groups);
        }
        if ($collection){
            return $groups;
        }
        $ids = array();
        foreach ($groups as $group){
            $ids[] = $group->getGroupId();
        }
        return $ids;
        
    }
    /*
     * object Varien Collection - collection of subscribers
     */
    public function removeSubscribersFromGroup($collection, $groupId){
        
        if (!$collection){
            return true;
        }
        $collection = Mage::getModel("ngroups/ngroupitems")->getCollection()
                                                            ->addFieldToFilter('subscriber_id', $collection)
                                                            ->addFieldToFilter('group_id', $groupId);
        
        foreach ($collection as $subscriber){
            $subscriber->delete();
        }
        return true;        
    
    }
    
    public function convertEmailsToSubscribers($emailsString, $store = false){
        
         // Get emails from test fields
        $emails = nl2br($emailsString);

        $newEmString = array();
        if (isset($emails) && $emails != "") {
            $mails = explode('<br />', $emails);
            foreach ($mails as $mail) {

                try {
                    if (!Zend_Validate::is($mail, 'EmailAddress')) {

                    }
                    if ($mail && $mail != "") {
                        $status = Mage::getModel('newsletter/subscriber')->setImportMode(true)->setNewStoreId($store)->subscribe(trim($mail));
                        if ($status > 0) {
                            $user = Mage::getModel('newsletter/subscriber')->loadByEmail(trim($mail));
                            $id = $user->getId();
                            $user->confirm($user->getCode());
                            $newEmString [] = $id;
                        }
                    }
                } catch (Mage_Core_Exception $e) {

                } catch (Exception $e) {

                }
            }
        }
        
        return $newEmString;
        
    }
       
    public function importEmailsToSubscribers($data, $store = false) {
        
        
        $newEmString = array();
        if (isset($data) && $data != "") {
            foreach ($data as $row) {
                
                    $row['mail'] = trim($row[0]);
                    unset($row[0]);
                 
                    if(isset($row[1])){
                    $row['custom_subscriber_name'] =  trim($row[1]);
                        unset($row[1]);
                    }
                    if(isset($row[2])){
                        $row['custom_subscriber_telephone'] = trim($row[2]);
                        unset($row[2]);
                    }
                $email = $row['mail'];
               
                    try {
                        if (Zend_Validate::is($email, 'EmailAddress')) {
                            if ($row && $row != "") {
                                $status = Mage::getModel('newsletter/subscriber')->setImportMode(true)->setNewStoreId($store)->subscribe($email);
                                
                                if ($status > 0) {
                                    $additionallData = $row;
                                    $user = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
                                    $id = $user->getId();
                                    $user->confirm($user->getCode());
                                    $newEmString []= $id;
                                    Mage::getModel('newsletter/subscriber')->setData($additionallData)->setId($id)->save();
                                    //$new_subscriber = Mage::getModel('newsletter/subscriber')->load($id);
                                    //$new_subscriber->setData($additionallData);
                                    //$new_subscriber->save();
                                }
                            }
                        } 
                    }
                    catch (Mage_Core_Exception $e) {Mage::getSingleton('adminhtml/session')->addError($e->getMessage());} 
                    catch (Exception $e) {Mage::getSingleton('adminhtml/session')->addError($e->getMessage());}
            }
            if ($newEmString[0] == 0) {
                $newEmString[0] = 'Empty';   
            }
        }
        
        return $newEmString;
    }  
    
    public function saveSubscribers($subscribers, $groupId) {
            foreach ($subscribers as $subscriber) {
                    Mage::getModel("ngroups/ngroupitems")->setData(array('group_id'=>$groupId, 'subscriber_id'=>$subscriber))->setId(null)->save();
            }
            
        return true;    
    }
    public function addSubscriber($subscriberId = false, $groupId, $email = false){
        
        if ($email){
            $subscriberId = Mage::getModel("newsletter/subscriber")->loadByEmail($email)->getId();
        }
        if (!$subscriberId){
            return false;
        }
        
        $isExists = Mage::getModel("ngroups/ngroupitems")->getCollection()
                                                ->addFieldToFilter('group_id', $groupId)
                                                ->addFieldToFilter('subscriber_id', $subscriberId)
                                                ->getFirstItem();
        if (!$isExists->getData()){
            Mage::getModel("ngroups/ngroupitems")->setData(array('group_id'=>$groupId, 'subscriber_id'=>$subscriberId))->setId(null)->save();
            return true;
        }
        return false;
        
    }
    public function removeSubscriber($subscriberId = false, $groupId = false, $email = false, $all = false){

        if ($email){
            $subscriberId = Mage::getModel("newsletter/subscriber")->loadByEmail($email)->getId();
        }
        if (!$subscriberId){
            return false;
        }
        if (!$all){
            $isExists = Mage::getModel("ngroups/ngroupitems")->getCollection()
                                                    ->addFieldToFilter('group_id', $groupId)
                                                    ->addFieldToFilter('subscriber_id', $subscriberId)
                                                    ->getFirstItem();
            if ($isExists->getData()){
                Mage::getModel("ngroups/ngroupitems")->load($isExists->getId())->delete();
                return true;
            }
        }else{
            
            $existsItems = Mage::getModel("ngroups/ngroupitems")->getCollection()
                                                    ->addFieldToFilter('subscriber_id', $subscriberId);
            foreach ($existsItems as $existsItem){
                Mage::getModel("ngroups/ngroupitems")->load($existsItem->getId())->delete();
            }
        }
        return false;
        
    }
    
    public function getVisibleGroups(){
        
        return $this->getCollection()->addFieldToFilter('visible', '1');
        
    }
    public function isSubscribed($groupId, $subscriberId = false){
        
        if (!$subscriberId){
            $subscriberId = Mage::helper('ngroups')->getSubscriberIdByEmail(Mage::getSingleton('customer/session')->getCustomer()->getEmail());
            if (!$subscriberId){
                return false;
            }
        }
        $isExists = Mage::getModel("ngroups/ngroupitems")->getCollection()
                                                    ->addFieldToFilter('group_id', $groupId)
                                                    ->addFieldToFilter('subscriber_id', $subscriberId)
                                                    ->getFirstItem();
        if (!$isExists->getData()){
            return false;
        }
        return true;
        
    }
    
    public function getSubscribersInStores($subscribers, $stores) {
        
        $list = array();
        foreach($stores as $key => $store_id) {
            $list = array_merge($list, Mage::getModel('newsletter/subscriber')->getCollection()->addFieldToFilter("store_id", $store_id)
                    ->getAllIds());
                    //->getData());
            
        }
        
        $subscribers = array_intersect($list, $subscribers);
        
        return $subscribers;
    }
    
   
}