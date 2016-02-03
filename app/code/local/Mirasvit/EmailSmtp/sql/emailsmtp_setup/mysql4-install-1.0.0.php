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
    DROP TABLE IF EXISTS `{$installer->getTable('emailsmtp/mail')}`;
    CREATE TABLE IF NOT EXISTS {$this->getTable('emailsmtp/mail')} (
        `mail_id`                 int(11)      NOT NULL AUTO_INCREMENT,
        `subject`                 varchar(255) NOT NULL,
        `is_plain`                tinyint(1)   NOT NULL DEFAULT '0',
        `body`                    text         NOT NULL,
        `message`                 text         NULL,
        `from_email`              varchar(255) NOT NULL,
        `from_name`               varchar(255) NOT NULL,
        `to_email`                varchar(255) NOT NULL,
        `to_name`                 varchar(255) NOT NULL,
        `reply_to`                varchar(255) NULL,
        `created_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY (`mail_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");

$installer->endSetup();
?>