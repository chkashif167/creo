<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    6th Jan, 2015
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    Review reminder data helper
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Clarion_ReviewReminder_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config extension enabled
     *
     * @var string
     */
    const XML_PATH_EXTENSION_ENABLED = 'review_reminder/status/extension_enable';
    /**
     * Config order status
     *
     * @var string
     */
    const XML_PATH_ORDER_STATUS = 'review_reminder/general_settings/order_status';
    
    /**
     * Config number of days after order placed
     *
     * @var string
     */
    const XML_PATH_NUM_OF_DAYS_AFTER_ORDER = 'review_reminder/general_settings/number_of_days';
    
    /**
     * check is reminder data already added
     *
     * @param $productId 
     * @param $cutomerId
     * @return boolean
     */
    public function isReminderExist($productId, $cutomerId)
    {
        if(empty($productId) || empty($cutomerId)){
            return false;
        }
        
        $collection = Mage::getModel('clarion_reviewreminder/reviewreminder')->getCollection()
                ->addFieldToFilter('customer_id', $cutomerId)
                ->addFieldToFilter('product_id', $productId);
        //echo $collection->getSelect();
        
        if($collection->count() > 0){
          return true;  
        }
        return false;
    }
    
    /**
     * Get config order status
     *
     * @param integer|string|Mage_Core_Model_Store $store 
     * @return string
     */
    public function getConfigOrderStatus($store = null)
    {
         return Mage::getStoreConfig(self::XML_PATH_ORDER_STATUS, $store);
    }
    
    /**
     * Get order status
     *
     * @param Clarion_ReviewReminder_Model_Reviewreminder $reminder  
     * @return string
     */
    public function getOrderStatus($reminder)
    {
        $orderId = $reminder->getOrderId();
        $order = Mage::getModel('sales/order')->load($orderId);
        $status = $order->getStatus();
        
        if($status){
            return $status;
        }
        return false;
    }
    
    /**
     * Check is order status match with config order status
     *
     * @param Clarion_ReviewReminder_Model_Reviewreminder $reminder 
     * @return boolean
     */
    public function isOrderStatusMatchWithConfig($reminder)
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $configOrderStatus = $this->getConfigOrderStatus($storeId);
        $orderStatus = $this->getOrderStatus($reminder);
        
        if($configOrderStatus == $orderStatus){
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Get config number of days after order placed
     *
     * @param integer|string|Mage_Core_Model_Store $store 
     * @return int
     */
    public function getConfigNumOfDaysAfterOrder($store = null)
    {
         return Mage::getStoreConfig(self::XML_PATH_NUM_OF_DAYS_AFTER_ORDER, $store);
    }
    
    /**
     * Get order date
     *
     * @param Clarion_ReviewReminder_Model_Reviewreminder $reminder
     * @return string
     */
    public function getOrderDate($reminder)
    {
        $orderId = $reminder->getOrderId();
        $order = Mage::getModel('sales/order')->load($orderId);
        $orderDate = $order->getCreatedAt();
        
        if($orderDate){
            return $orderDate;
        }
        return false;
    }
    
    /**
     * Get number of days after order placed
     *
     * @param $orderDate
     * @return int
     */
    public function getNumOfDaysAfterOrder($orderDate)
    {
        $days = 0;
        if (version_compare(phpversion(), '5.3.0', '<')===true) {
            $diff = (time() - strtotime($orderDate));
            $days = floor($diff/(60*60*24));
        } else {
            $currentDate = new DateTime();
            $objOrderDate = new DateTime($orderDate);
            $interval = $currentDate->diff($objOrderDate);
            $days = $interval->d;
        }
        return $days;
    }
    
    /**
     * check is match number of days after order placed with config number of days
     *
     * @param Clarion_ReviewReminder_Model_Reviewreminder $reminder
     * @return boolean
     */
    public function isMatchNumOfDaysAfterOrder($reminder)
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $configNumOfDaysAfterOrder = $this->getConfigNumOfDaysAfterOrder($storeId);
        $orderDate = $this->getOrderDate($reminder);
        $numOfDaysAfterOrder = $this->getNumOfDaysAfterOrder($orderDate);
        
         if($numOfDaysAfterOrder >= $configNumOfDaysAfterOrder){
            return true;
         } else {
            return false;
         }
    }
    
    /**
     * check is reminder satishfies all the config conditions.
     *
     * @param Clarion_ReviewReminder_Model_Reviewreminder $reminder
     * @return boolean
     */
    public function isMatchAllConfigSettings($reminder)
    {
        if(!$this->isOrderStatusMatchWithConfig($reminder)){
            return false;
        }
        
        if(!$this->isMatchNumOfDaysAfterOrder($reminder)){
            return false;
        }
        
       return true;
    }
    
    /**
     * Check is review already added by customer
     *
     * @param $productId 
     * @param $cutomerId
     * @return boolean
     */
    public function isReviewAlreadyAdded($productId, $cutomerId)
    {
        if(empty($productId) || empty($cutomerId)){
            return false;
        }
        
        $collection = Mage::getModel('review/review')->getProductCollection()
            ->addCustomerFilter($cutomerId)
            ->addEntityFilter($productId);
        
        //echo $collection->getSelect();
        //exit;
        
        if($collection->count() > 0){
          return true;  
        }
        return false;
    }
    
    /**
     * Check is extension enabled
     *
     * @return boolean
     */
    public function isExtensionEnabled()
    {
        if($this->getConfigExtensionEnabled()){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * Get config extension enabled
     *
     * @param integer|string|Mage_Core_Model_Store $store 
     * @return string
     */
    public function getConfigExtensionEnabled($store = null)
    {
         return Mage::getStoreConfig(self::XML_PATH_EXTENSION_ENABLED, $store);
    }
    
}