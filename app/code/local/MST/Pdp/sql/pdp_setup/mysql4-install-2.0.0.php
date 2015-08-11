<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_images')} (
		`image_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`image_type` varchar(255) NOT NULL DEFAULT '',
		`filename` varchar(500) NOT NULL DEFAULT '',
		`category` varchar(500) NOT NULL DEFAULT '',
		`color` text NOT NULL,
		`position` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`image_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_fonts')} (
		`font_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`name` varchar(255) NOT NULL DEFAULT '',
		`ext` varchar(500) NOT NULL DEFAULT '',
		PRIMARY KEY (`font_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_license')} (
		`license_id` int(11) NOT NULL AUTO_INCREMENT,
		`domain_count` varchar(255) NOT NULL,
		`domain_list` varchar(255) NOT NULL,
		`path` varchar(255) NOT NULL,
		`extension_code` varchar(255) NOT NULL,
		`license_key` varchar(255) NOT NULL,
		`created_time` date NOT NULL,
		`domains` varchar(255) NOT NULL,
		`is_valid` tinyint(1) NOT NULL DEFAULT '0',
		PRIMARY KEY (`license_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_act')} (
		`act_id` int(11) NOT NULL AUTO_INCREMENT,
		`domain_count` varchar(255) NOT NULL,
		`domain_list` varchar(255) NOT NULL,
		`path` varchar(255) NOT NULL,
		`extension_code` varchar(255) NOT NULL,
		`act_key` varchar(255) NOT NULL,
		`created_time` date NOT NULL,
		`domains` varchar(255) NOT NULL,
		`is_valid` tinyint(1) NOT NULL DEFAULT '0',
		PRIMARY KEY (`act_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_admin_template')} (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`product_id` int(11) NOT NULL,
		`pdp_design` text NOT NULL,
		`status` smallint(6) NOT NULL DEFAULT '1',
		`created_time` datetime DEFAULT NULL,
		`update_time` datetime DEFAULT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_artwork_category')} (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`title` varchar(500) NOT NULL,
		`status` smallint(2) NOT NULL DEFAULT '1',
		`position` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_image_color')} (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`image_id` int(11) unsigned NOT NULL,
		`filename` varchar(500) NOT NULL DEFAULT '',
		`color` varchar(500) NOT NULL DEFAULT '',
		`filename_back` varchar(500) NOT NULL DEFAULT '',
		PRIMARY KEY (`id`),
		KEY `image_id` (`image_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_colors')} (
		`color_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`color_name` varchar(255) NOT NULL DEFAULT '',
		`color_code` varchar(500) NOT NULL DEFAULT '',
		`status` smallint(11) NOT NULL DEFAULT '1',
		`position` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`color_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_multisides')} (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`product_id` int(11) unsigned NOT NULL,
		`label` varchar(500) NOT NULL,
		`color_id` int(11) unsigned NOT NULL,
		`inlay_w` varchar(255) NOT NULL,
		`inlay_h` varchar(255) NOT NULL,
		`inlay_t` varchar(255) NOT NULL,
		`inlay_l` varchar(255) NOT NULL,
		`filename` varchar(500) NOT NULL,
		`position` int(11) NOT NULL DEFAULT '0',
		`status` smallint(2) NOT NULL DEFAULT '1',
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_multisides_colors')} (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`product_id` int(11) unsigned NOT NULL,
		`color_id` int(11) unsigned NOT NULL,
		`status` smallint(2) NOT NULL DEFAULT '1',
		`position` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_multisides_colors_images')} (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`product_color_id` int(11) unsigned NOT NULL,
		`side_id` int(11) unsigned NOT NULL,
		`filename` varchar(500) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_product_status')} (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`product_id` int(11) unsigned NOT NULL,
		`note` varchar(500) NOT NULL,
		`status` smallint(2) NOT NULL DEFAULT '1',
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdpdesign_share')} (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`product_id` int(11) unsigned NOT NULL,
		`pdpdesign` text NOT NULL,
		`url` text NOT NULL,
		`note` varchar(500) NOT NULL,
		`status` smallint(2) NOT NULL DEFAULT '1',
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup(); 
