<?php
class Tentura_Ngroups_Model_Subscriber extends Mage_Newsletter_Model_Subscriber{
    
    /**
     * Unsubscribes loaded subscription
     *
     */
    public function unsubscribe()
    {
        if ($this->hasCheckCode() && $this->getCode() != $this->getCheckCode()) {
            Mage::throwException(Mage::helper('newsletter')->__('Invalid subscription confirmation code.'));
        }

        $this->setSubscriberStatus(self::STATUS_UNSUBSCRIBED)
            ->save();
        $this->sendUnsubscriptionEmail();
        
        $items = Mage::getModel("ngroups/ngroupitems")->getCollection()->addFieldToFilter("subscriber_id",$this->getId());
        foreach ($items as $item){
            $item->delete();
        }
        
        
        return $this;
    }
    
}