<?php
class Tentura_Ngroups_Block_Adminhtml_Ngroups_Render_Number extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function render(Varien_Object $row){
        
        $model = Mage::getModel("ngroups/ngroups");
        
        
        // collection of all subscribers in current group || have subscriber_id
        $subscribersInGroupCollection = $model->getGroupSubscribers($row->getId())->getData();
        
        // store view id of current newsletter group
        $store_ids = $model->load($row->getId())->getStoreIds();
        
        // collection of all subscribers || have subscriber_id & customer_id || if guest - customer_id is 0
        $subscribersCollection = Mage::getModel("newsletter/subscriber")->getCollection()->getData();
         if($store_ids != '') {
            $store_ids = explode(',', $store_ids);
        }else{
            $store_ids = NULL;
        }
        
        // quantity of subscribers in current group
        $subscribers = $model->getGroupSubscribers($row->getId(), true, $store_ids); 
        $subscribers_in_group_all = $model->getGroupSubscribers($row->getId(), true); 
        
        
        
        $guests_number = 0;
        $iterator = 0;
        $string_query = '';
        if(is_array($store_ids)){
            asort($store_ids);
            foreach($store_ids as $key=>$value){

                $guests_number = $guests_number + sizeof(Mage::getModel('newsletter/subscriber')
                                                                ->getCollection()
                                                                ->addFieldToFilter("customer_id", 0)
                                                                ->addFieldToFilter('store_id', $value)
                                                            );
                if($iterator == 0) {
                    $operator = 'AND';
                }else {
                    $operator = 'OR';
                }
                $string_query .= $operator . ' store_id = ' . $value . ' ';
                $iterator++;
            }
        }else{
            $guests_number = $guests_number + sizeof(Mage::getModel('newsletter/subscriber')
                                                                ->getCollection()
                                                                ->addFieldToFilter("customer_id", 0)
                                                            );
        }
        
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $queryNumberOfCurrentGroup ="SELECT group_id, count(group_id) 
                                            FROM ".$resource->getTableName('customer_entity')."
                                            WHERE entity_id  IN 
                                                (
                                                    SELECT customer_id 
                                                    FROM ".$resource->getTableName('newsletter_subscriber')." 
                                                    
                                                ) ". $string_query ." GROUP BY group_id
                                                
                                            ";
        
        $SubscribersData = $readConnection->fetchAll($queryNumberOfCurrentGroup);
        
        $SubscribersDataNewOrder[0]['group_id'] = 0;
        $SubscribersDataNewOrder[0]['count(group_id)'] = $guests_number;
        for ($i=0;$i<sizeof($SubscribersData);$i++){  
            $SubscribersDataNewOrder[$SubscribersData[$i]['group_id']]=$SubscribersData[$i];
        }
         
        $countAddedSubscribers = 0;
        $string = "";
        $hasGuest = false;
        $groupSubscribers = array();
        $subscribersNumber = 0;
        $doubleNumber = 0;
        if ($row->getCustomerGroups() != "") {
            $string .= "<div class='ngroups_grid_groupsinfo'><h5>".Mage::helper("ngroups")->__("Assigned to User groups:")."
            </h5><div class=\"grid\"><table class=\"border\" cellspacing='0' ><tbody><tr class=\"headings\"><th><strong>Group name</strong></th><th><strong>Subscribers in group</strong></th></tr><p>";
            $cGroups = explode(',', $row->getCustomerGroups());
            $doubleSubscribers = $readConnection->fetchAll("SELECT group_id, count(group_id) 
                                            FROM ".$resource->getTableName('customer_entity')."
                                            WHERE entity_id  IN 
                                                (
                                                    SELECT customer_id 
                                                    FROM ".$resource->getTableName('newsletter_subscriber')."
                                                    WHERE subscriber_id IN
                                                        (
                                                            SELECT subscriber_id
                                                            FROM ".$resource->getTableName('ngroups_items')."
                                                            WHERE group_id = ".$row->getId()."    
                                                        )
                                                )  GROUP BY group_id
                                            ");
            
            
            $DoubleNewData = array();
            for ($i=0;$i<sizeof($doubleSubscribers); $i++){
                $DoubleNewData[$doubleSubscribers[$i]['group_id']]=$doubleSubscribers[$i];
            }
        
            /// for current group of users            
            foreach ($cGroups as $cGroup) { 
                if ($cGroup == 0) {
                    $hasGuest = true;
                }
                $groupName = Mage::getModel("customer/group")->load($cGroup)->getCode();
                $string .= Mage::helper("ngroups")->__("<tr><td>").$groupName.Mage::helper("ngroups")->__("</td><td>");
                $string.=(isset($SubscribersDataNewOrder[$cGroup]['group_id']) && $SubscribersDataNewOrder[$cGroup]['group_id']==$cGroup)?
                $SubscribersDataNewOrder[$cGroup]['count(group_id)'].Mage::helper("ngroups")->__("</tr>"):
                Mage::helper("ngroups")->__("0</tr>");
                
                if (isset($SubscribersDataNewOrder[$cGroup]['group_id']) && $SubscribersDataNewOrder[$cGroup]['group_id']==$cGroup) {
                    $subscribersNumber = $subscribersNumber + $SubscribersDataNewOrder[$cGroup]['count(group_id)'];
                   if (isset($DoubleNewData[$cGroup])){
                        $doubleNumber = $doubleNumber + $DoubleNewData[$cGroup]['count(group_id)'];
                   }
                } 
            }
           
            $string .= "</p></tbody></table></div></div>";
           
            if (!$hasGuest) {
                $add_lost_subscriber = $subscribers_in_group_all - $subscribers;  /// double filter return doubles with unchcking store view. This is fixed wrong calculation
                $subscribers =  $subscribers + $subscribersNumber - $doubleNumber + $add_lost_subscriber;
            } else {
                if ($subscribers != 0) {
                    $guestsInNgroup = $readConnection->fetchAll("SELECT subscriber_id, group_id, count(subscriber_id) 
                                                     FROM ".$resource->getTableName('ngroups_items')."
                                                     WHERE subscriber_id  IN 
                                                         (
                                                             SELECT subscriber_id 
                                                             FROM ".$resource->getTableName('newsletter_subscriber')."
                                                             WHERE customer_id = 0                                                         
                                                         ) AND group_id = ".$row->getId()." 
                                                     ");
                } else {
                    $guestsInNgroup = 0;
                }
                              
                if (count($guestsInNgroup) == 1 && $guestsInNgroup[0]['subscriber_id'] == NULL){
                    $guestsQuantity = 0;
                } else {
                    $guestsQuantity = $guestsInNgroup[0]["count(subscriber_id)"];
                }
                
                $subscribers =  $subscribers + $subscribersNumber - $doubleNumber - $guestsQuantity;
            }
        } else {
            
            return Mage::helper("core")->__("Inserted in user group customers: %s", $subscribers);
            
        }
       
        $subscribers = Mage::helper("core")->__("Inserted in user group customers: %s", $subscribers);
        
        return $subscribers.$string;
       

    }
}
