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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('advd/notification')}`;
CREATE TABLE `{$this->getTable('advd/notification')}` (
    `notification_id` int(11)      NOT NULL AUTO_INCREMENT,
    `user_id`         int(11)      NOT NULL,
    `is_active`       int(1)       NOT NULL DEFAULT '0',

    `email_subject`   varchar(255) NOT NULL,
    `recipient_email` text NOT NULL,
    `email_widgets`   text NULL,

    `schedule_day`    varchar(255) NULL,
    `schedule_time`   varchar(255) NULL,

    `sent_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',

    `created_at`      datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at`      datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
