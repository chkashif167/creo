<?php
/**
 * Create sliders table
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
$installer = $this;
$installer->startSetup();
$installer->run("
		
CREATE TABLE IF NOT EXISTS `{$this->getTable('auguria_sliders/sliders')}` (
  `slider_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NULL DEFAULT '',
  `image` varchar(255) NULL DEFAULT '',
  `link` varchar(255) NULL DEFAULT '',
  `cms_content` varchar(255) NULL DEFAULT '',
  `sort_order` int(11) NULL DEFAULT 0,
  `is_active` tinyint(1) NULL DEFAULT '1',
  PRIMARY KEY  (`slider_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('auguria_sliders/stores')}` (
  `slider_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`slider_id`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('auguria_sliders/pages')}` (
  `slider_id` int(11) unsigned NOT NULL auto_increment,
  `page_id` smallint(6) NOT NULL,
  PRIMARY KEY (`slider_id`,`page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('auguria_sliders/categories')}` (
  `slider_id` int(11) unsigned NOT NULL auto_increment,
  `category_id` int(10) NOT NULL,
  PRIMARY KEY (`slider_id`,`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");
$installer->endSetup();