<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('feedexport/template')}`;
CREATE TABLE `{$this->getTable('feedexport/template')}` (
    `template_id`        int(11)      NOT NULL AUTO_INCREMENT,
    `name`               varchar(255) NOT NULL,
    `type`               varchar(255) NOT NULL DEFAULT 'csv',
    `format`             longtext     NULL,
    `created_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{$this->getTable('feedexport/feed')}`;
CREATE TABLE `{$this->getTable('feedexport/feed')}` (
    `feed_id`            int(11)      NOT NULL AUTO_INCREMENT,
    `name`               varchar(255) NOT NULL,
    `store_id`           smallint(5)  UNSIGNED NOT NULL,

    `filename`           varchar(255) NOT NULL,
    `type`               varchar(255) NOT NULL DEFAULT 'csv',
    `format`             longtext     NULL,

    `is_active`          int(1)       NOT NULL DEFAULT '0',

    `generated_at`       datetime     NULL,
    `generated_cnt`      int(11)      NULL,
    `generated_time`     int(11)      NULL,

    `cron`               int(1)       NOT NULL DEFAULT '0',
    `cron_day`           varchar(255) NULL,
    `cron_time`          varchar(255) NULL,

    `ftp`                int(1)       NOT NULL DEFAULT '0',
    `ftp_host`           varchar(255) NULL,
    `ftp_user`           varchar(255) NULL,
    `ftp_password`       varchar(255) NULL,
    `ftp_path`           varchar(255) NULL,
    `ftp_passive_mode`   int(1)       NULL,
    `uploaded_at`        datetime     NULL,

    `created_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`feed_id`),
    CONSTRAINT `FK_FEEDEXPORT_FEED_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{$this->getTable('feedexport/custom_attribute')}`;
CREATE TABLE {$this->getTable('feedexport/custom_attribute')} (
    `attribute_id`          int(11)      unsigned NOT NULL auto_increment,
    `name`                  varchar(255) NOT NULL,
    `code`                  varchar(255) NOT NULL,
    `conditions_serialized` text         NOT NULL,
    PRIMARY KEY  (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();