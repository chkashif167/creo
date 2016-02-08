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
    DROP TABLE IF EXISTS `{$installer->getTable('emailreport/open')}`;
    CREATE TABLE `{$installer->getTable('emailreport/open')}` (
        `id`                 int(11)       NOT NULL AUTO_INCREMENT,
        `queue_id`           int(11)       NOT NULL,
        `trigger_id`         int(11)       NOT NULL,
        `session_id`         varchar(255)  NOT NULL,

        `created_at`         datetime      NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`id`),
        UNIQUE KEY `IDX_BASE` (`queue_id`, `trigger_id`, `session_id`)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `{$installer->getTable('emailreport/click')}`;
    CREATE TABLE `{$installer->getTable('emailreport/click')}` (
        `id`                 int(11)       NOT NULL AUTO_INCREMENT,
        `queue_id`           int(11)       NOT NULL,
        `trigger_id`         int(11)       NOT NULL,
        `session_id`         varchar(255)  NOT NULL,

        `created_at`         datetime      NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`id`),
        UNIQUE KEY `IDX_BASE` (`queue_id`, `trigger_id`, `session_id`)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `{$installer->getTable('emailreport/order')}`;
    CREATE TABLE `{$installer->getTable('emailreport/order')}` (
        `id`                 int(11)       NOT NULL AUTO_INCREMENT,
        `queue_id`           int(11)       NOT NULL,
        `trigger_id`         int(11)       NOT NULL,
        `session_id`         varchar(255)  NOT NULL,

        `revenue`            decimal(12,4) NOT NULL,
        `coupon`             varchar(255)  NULL,

        `created_at`         datetime      NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`id`),
        UNIQUE KEY `IDX_BASE` (`queue_id`, `trigger_id`, `session_id`)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `{$installer->getTable('emailreport/review')}`;
    CREATE TABLE `{$installer->getTable('emailreport/review')}` (
        `id`                 int(11)       NOT NULL AUTO_INCREMENT,
        `queue_id`           int(11)       NOT NULL,
        `trigger_id`         int(11)       NOT NULL,
        `session_id`         varchar(255)  NOT NULL,

        `review_id`          int(11)       NOT NULL,

        `created_at`         datetime      NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`id`),
        UNIQUE KEY `IDX_BASE` (`queue_id`, `trigger_id`, `session_id`)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `{$this->getTable('emailreport/aggregated')}`;
    CREATE TABLE `{$this->getTable('emailreport/aggregated')}` (
        `id`                 int(11)        NOT NULL AUTO_INCREMENT,
        `trigger_id`         int(11)        NOT NULL,
        `period`             date           NOT NULL,
        `emails`             int(11)        NULL,
        `opens`              int(11)        NULL,
        `clicks`             int(11)        NULL,
        `orders`             int(11)        NULL,
        `revenue`            decimal(12,4)  NULL,
        `reviews`            int(11)        NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `IDX_BASE` (`trigger_id`, `period`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
