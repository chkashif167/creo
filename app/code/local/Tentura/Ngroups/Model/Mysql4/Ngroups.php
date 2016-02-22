<?php

class Tentura_Ngroups_Model_Mysql4_Ngroups extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the ngroups_id refers to the key field in your database table.
        $this->_init('ngroups/ngroups', 'ngroups_id');
    }
    public function setSubscribers($groupId, $newCustomers = array(), $deletedCustomers = array())
    {

        $select = $this->_getWriteAdapter()->select();
        $select->from($this->getTable('newsletter/queue_link'),'queue_id')
            ->where('group_id = ?', $groupId);
            
        $queueIds = $this->_getWriteAdapter()->fetchCol($select);
        $queueIds = array_unique($queueIds);

        $groupSubscribers = Mage::getModel("ngroups/ngroups")->getGroupSubscribers($groupId);

        foreach ($queueIds as $queueId){

            try{
                
                foreach($newCustomers as $subscriberId) {

                    $data = array();
                    $data['queue_id'] = $queueId;
                    $data['subscriber_id'] = $subscriberId;
                    $data['group_id'] = $groupId;
                    $this->_getWriteAdapter()->insert($this->getTable('newsletter/queue_link'), $data);
                
                }
            
            }catch (Exception $e) {
            
                
            }
            
        }

    }
    /*
     * v2.0 checked
     */
    public function unsetSubscribers($groupId, $subscriberIds = array())
    {

        $where = '';
        $where = $this->_getReadAdapter()->quoteInto('subscriber_id in (?)', $subscriberIds);
        $where .= " and group_id=".$groupId;

        $this->_getWriteAdapter()->delete($this->getTable('newsletter/queue_link'),$where);

        return true;

    }
    
}