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
    DROP TABLE IF EXISTS `{$installer->getTable('email/unsubscription')}`;
    CREATE TABLE `{$installer->getTable('email/unsubscription')}` (
        `unsubscription_id` int(11)      NOT NULL AUTO_INCREMENT,

        `email`             varchar(255) NOT NULL,
        `trigger_id`        int(11)      NULL,

        `created_at`        datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`        datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`unsubscription_id`)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8;
");

$installer->endSetup();
