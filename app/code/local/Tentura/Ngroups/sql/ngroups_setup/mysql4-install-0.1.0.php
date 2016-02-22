<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('ngroups')};
CREATE TABLE {$this->getTable('ngroups')} (
  `ngroups_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `customers` text NOT NULL,
  `visible` INT NOT NULL,
  PRIMARY KEY (`ngroups_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

ALTER TABLE {$this->getTable('newsletter_template')} ADD `user_group` INT NOT NULL;

ALTER TABLE {$this->getTable('newsletter_queue_link')} ADD `group_id` INT NOT NULL;


    ");

$installer->endSetup(); 