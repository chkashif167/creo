<?php
//some settings
define('MAGENTO_ROOT', getcwd()); 
$mageFilename = MAGENTO_ROOT . '/app/Mage.php'; 
require_once $mageFilename; 
//Mage::setIsDeveloperMode(true); 
ini_set('display_errors', 1); 
umask(0);
//instantiate the app model
Mage::app('admin'); 

// get newsletter queue collection
$collection = Mage::getResourceModel('newsletter/queue_collection')
            ->addSubscribersInfo();

//go through every one look the status
foreach ($collection->getItems() as $item) {

    //need to delete all unsent items
    if($item->getStatus() == Mage_Newsletter_Model_Queue::STATUS_NEVER) {
        $item->delete();    
        continue;
        }

    //need to delete all canceled items
    if($item->getStatus() == Mage_Newsletter_Model_Queue::STATUS_CANCEL) {
        $item->delete();
        continue;   
        }

    //need to delete all canceled items
    if($item->getStatus() == Mage_Newsletter_Model_Queue::STATUS_PAUSE) {
        $item->delete();
        continue;   
        }
 }