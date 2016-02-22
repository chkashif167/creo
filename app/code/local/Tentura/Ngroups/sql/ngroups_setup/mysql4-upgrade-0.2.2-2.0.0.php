<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('ngroups')} ADD `customer_groups` VARCHAR(500) NOT NULL;

ALTER TABLE {$this->getTable('newsletter_queue')} ADD `user_group` int(11) NOT NULL DEFAULT '0';


CREATE TABLE {$this->getTable('ngroups_items')} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `subscriber_id` int(11) NOT NULL DEFAULT '0',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    ");

$ngroups = Mage::getModel('ngroups/ngroups')->getCollection();
foreach ($ngroups as $group){

    $subscribers = $group->getCustomers();
    $subscribers = explode(",", $subscribers);
    foreach ($subscribers as $subscriber){
        if ($subscriber){
            Mage::getModel("ngroups/ngroupitems")->setData(array("group_id"=>$group->getId(), "subscriber_id"=>$subscriber))->setId(null)->save();
        }
    }

}

$installer->endSetup(); 