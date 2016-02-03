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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


$installer = $this;

$installer->startSetup();
$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('email/trigger')}`;
    CREATE TABLE `{$installer->getTable('email/trigger')}` (
        `trigger_id`              int(11)      NOT NULL AUTO_INCREMENT,
        `title`                   varchar(255) NOT NULL,
        `description`             text         NULL,
        `store_ids`               varchar(255) NOT NULL,
        `is_active`               int(1)       NOT NULL DEFAULT 0,
        `active_from`             datetime     NULL,
        `active_to`               datetime     NULL,

        `trigger_type`            varchar(255) NOT NULL,
        `event`                   varchar(255) NULL,
        `cancellation_event`      text         NULL,
        `schedule`                varchar(255) NULL,

        `run_rule_id`             int(11)      NULL,
        `stop_rule_id`            int(11)      NULL,

        `sender_email`            varchar(255) NULL,
        `sender_name`             varchar(255) NULL,

        `copy_email`              text NULL,

        `ga_source`               varchar(255) NULL,
        `ga_medium`               varchar(255) NULL,
        `ga_term`                 varchar(255) NULL,
        `ga_content`              varchar(255) NULL,
        `ga_name`                 varchar(255) NULL,

        `created_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`trigger_id`)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `{$installer->getTable('email/rule')}`;
    CREATE TABLE `{$installer->getTable('email/rule')}` (
        `rule_id`                 int(11)      NOT NULL AUTO_INCREMENT,
        `title`                   varchar(255) NOT NULL,
        `description`             text         NULL,
        `is_active`               int(1)       NOT NULL DEFAULT 0,
        `is_system`               int(1)       NOT NULL DEFAULT 0,

        `conditions_serialized`   text         NOT NULL,
        `actions_serialized`      text         NOT NULL,

        `created_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`rule_id`)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `{$installer->getTable('email/trigger_chain')}`;
    CREATE TABLE `{$installer->getTable('email/trigger_chain')}` (
        `chain_id`                 int(11)      NOT NULL AUTO_INCREMENT,
        `trigger_id`               int(11)      NOT NULL,

        `delay`                    int(11)      NULL,
        `template_id`              varchar(255) NOT NULL,

        `run_rule_id`              int(11)      NULL,
        `stop_rule_id`             int(11)      NULL,

        `coupon_enabled`           int(1)       NOT NULL DEFAULT 0,
        `coupon_sales_rule_id`     int(11)      NULL,
        `coupon_expires_days`      int(11)      NULL,

        `cross_sells_enabled`      int(1)       NOT NULL DEFAULT 0,
        `cross_sells_type_id`      varchar(255) NULL,

        `created_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`chain_id`)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `{$installer->getTable('email/queue')}`;
    CREATE TABLE `{$installer->getTable('email/queue')}` (
        `queue_id`                 int(11)      NOT NULL AUTO_INCREMENT,
        `status`                   varchar(255) NOT NULL,
        `trigger_id`               int(11)      NOT NULL,
        `chain_id`                 int(11)      NOT NULL,
        `uniq_key`                 text         NOT NULL,
        `uniq_key_md5`             varchar(255) NOT NULL,

        `scheduled_at`             datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `sent_at`                  datetime     NULL,
        `attemtps_number`          int(11)      NOT NULL DEFAULT 0,

        `sender_email`             varchar(255) NOT NULL,
        `sender_name`              varchar(255) NOT NULL,

        `recipient_email`          varchar(255) NOT NULL,
        `recipient_name`           varchar(255) NOT NULL,

        `subject`                  text         NULL,
        `content`                  text         NULL,
        `args_serialized`          longtext     NULL,

        `created_at`               datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`               datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`queue_id`)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8;
    ALTER TABLE `{$installer->getTable('email/queue')}` ADD INDEX (`status`);
    ALTER TABLE `{$installer->getTable('email/queue')}` ADD INDEX (`scheduled_at`);

    DROP TABLE IF EXISTS `{$installer->getTable('email/event')}`;
    CREATE TABLE `{$installer->getTable('email/event')}` (
        `event_id`                 int(11)      NOT NULL AUTO_INCREMENT,

        `uniq_key`                 text         NOT NULL,
        `code`                     varchar(255) NOT NULL,
        `args_serialized`          longtext     NULL,
        `processed`                int(1)      NOT NULL DEFAULT 0,

        `created_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`event_id`)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8;

");

$installer->endSetup();
