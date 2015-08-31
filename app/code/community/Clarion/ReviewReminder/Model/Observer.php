<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    6th Jan, 2015
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    Review reminder observer model
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Clarion_ReviewReminder_Model_Observer
{
    public function addReviewReminderInformation($observer)
    {
        //check is extension enabled
         if (!Mage::helper('clarion_reviewreminder')->isExtensionEnabled()) {
             return;
         }
         
        $event = $observer->getEvent();
        $orderIds = $event->getOrderIds();
        if(!empty($orderIds) && is_array($orderIds)){
            foreach ($orderIds as $orderId) {
                //check order id
                if(empty($orderId)){
                    continue;
                }
                
                //get order information
                $order = Mage::getModel('sales/order')->load($orderId);
                $customerId = $order->getCustomerId();
                //Mage::log("cid=".$customerId);
                //check customer id
                if(empty($customerId)){
                    continue;
                }
                    
                $currentTimestamp = time();

                //product ids from order ids
                $items = $order->getAllVisibleItems();
                //Mage::log($items);
                $productId = array();
                if(!empty($items) && is_array($items)){
                    foreach ($items as $item) {
                        $productIds[] = $item->getProductId();
                    }
                }
                // Mage::log($productIds);
                    
                //Save data
                if(!empty($productIds) && is_array($productIds)){

                    $transactionSave = Mage::getModel('core/resource_transaction');

                    foreach ($productIds as $productId) {
                        //Check is reminder exist
                        if(Mage::Helper('clarion_reviewreminder')->isReminderExist($productId, $customerId)){
                            continue;
                        }

                        //Check is review already added by customer
                        if(Mage::Helper('clarion_reviewreminder')->isReviewAlreadyAdded($productId, $customerId)){
                            continue;
                        }

                        //add reminder
                        $reviewreminder = Mage::getModel('clarion_reviewreminder/reviewreminder');

                        $reviewreminder->setOrderId($orderId);
                        $reviewreminder->setCustomerId($customerId);
                        $reviewreminder->setProductId($productId);
                        $reviewreminder->setCreatedAt($currentTimestamp);

                        $transactionSave->addObject($reviewreminder);
                    }
                    $transactionSave->save();
                }
            }
        }
    }
    
    /**
     * Update reminder after review added
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function reviewSaveAfter(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $review = $event->getObject();
        $productId = $review->getEntityPkValue();
        $cutomerId = $review->getCustomerId();
        
        if(empty($productId) || empty($cutomerId)){
            return false;
        }
        
        //Check is reminder exist
        $isRecordExist = Mage::Helper('clarion_reviewreminder')->isReminderExist($productId, $cutomerId);
        //update review flag
        if($isRecordExist){
            $collection = Mage::getModel('clarion_reviewreminder/reviewreminder')->getCollection()
                ->addFieldToFilter('customer_id', $cutomerId)
                ->addFieldToFilter('product_id', $productId);
        
            if($collection->count() > 0){
                foreach($collection as $reminder){
                    $reminder->setIsReviewAdded(1);
                    $reminder->save();
                }
            }
        }
    }
    
    /**
     * Cron job method to send product review reminder
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function sendReviewReminder(Mage_Cron_Model_Schedule $schedule)
    {
        //check is extension enabled
         if (!Mage::helper('clarion_reviewreminder')->isExtensionEnabled()) {
             return;
         }
         
        //get all records to send reminder
        $collection = Mage::getModel('clarion_reviewreminder/reviewreminder')->getCollection()
            ->addFieldToFilter('is_review_added', 0);
        
        if($collection->count() > 0){
            foreach ($collection as $reminder){
                $customerId = $reminder->getCustomerId();
                if(empty($customerId)){
                    continue;
                }
                
                //Check config settings
                if(!Mage::Helper('clarion_reviewreminder')->isMatchAllConfigSettings($reminder)){
                    continue;
                }
                
                //send reminder mail
                $isMailSent = Mage::Helper('clarion_reviewreminder/mail')->sendReminderEmail($reminder);
                
                //update reminder record
                if($isMailSent){
                    $reminder->setIsReminderSent(1);
                    $reminderCount = $reminder->getReminderCount();
                    //Increment reminder count by 1
                    $reminderCount++;
                    $reminder->setReminderCount($reminderCount);
                    $currentTimestamp = time();
                    $reminder->setSentAt($currentTimestamp);
                    $reminder->setUpdatedAt($currentTimestamp);
                    $reminder->save();
                }
            }
        }
    }
}
