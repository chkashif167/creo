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
DROP TABLE IF EXISTS `{$this->getTable('feedexport/feed_history')}`;
CREATE TABLE `{$this->getTable('feedexport/feed_history')}` (
    `history_id`         int(11)      NOT NULL AUTO_INCREMENT,
    `feed_id`            int(11)      NOT NULL,
    `type`               varchar(255) NULL,
    `title`              varchar(255) NOT NULL,
    `message`            longtext     NULL,
    `created_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`history_id`),
    CONSTRAINT `FK_FEEDEXPORT_FEED_HISTORY_FEED_ID` FOREIGN KEY (`feed_id`) REFERENCES {$this->getTable('feedexport/feed')} (`feed_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();