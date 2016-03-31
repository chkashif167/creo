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
DROP TABLE IF EXISTS `{$this->getTable('feedexport/rule')}`;
CREATE TABLE {$this->getTable('feedexport/rule')} (
    `rule_id`               int(11)      unsigned NOT NULL auto_increment,
    `name`                  varchar(255) NOT NULL,
    `type`                  varchar(255) NOT NULL,
    `conditions_serialized` text         NOT NULL,
    `actions_serialized`    text         NOT NULL,
    `is_active`             int(1)       NOT NULL DEFAULT '0',
    `created_at`            datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at`            datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{$this->getTable('feedexport/rule_product')}`;
CREATE TABLE {$this->getTable('feedexport/rule_product')} (
    `id`                    int(11)      UNSIGNED NOT NULL auto_increment,
    `rule_id`               int(11)      UNSIGNED NOT NULL DEFAULT '0',
    `product_id`            int(10)      UNSIGNED NOT NULL DEFAULT '0',
    `store_id`              smallint(5)  UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY  (`id`),
    UNIQUE KEY `IDX_BASE` (`rule_id`, `product_id`, `store_id`),
    CONSTRAINT `FK_FEEDEXPORT_RULE_PRODUCT_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_FEEDEXPORT_RULE_PRODUCT_RULE` FOREIGN KEY (`rule_id`) REFERENCES {$this->getTable('feedexport/rule')} (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_FEEDEXPORT_RULE_PRODUCT_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES {$this->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{$this->getTable('feedexport/rule_feed')}`;
CREATE TABLE {$this->getTable('feedexport/rule_feed')} (
    `id`                    int(11)      NOT NULL auto_increment,
    `rule_id`               int(11)      UNSIGNED NOT NULL DEFAULT '0',
    `feed_id`               int(11)      NOT NULL DEFAULT '0',
    PRIMARY KEY  (`id`),
    UNIQUE KEY `IDX_BASE` (`rule_id`, `feed_id`),
    CONSTRAINT `FK_FEEDEXPORT_RULE_FEED_RULE` FOREIGN KEY (`rule_id`) REFERENCES {$this->getTable('feedexport/rule')} (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_FEEDEXPORT_RULE_FEED_FEED` FOREIGN KEY (`feed_id`) REFERENCES {$this->getTable('feedexport/feed')} (`feed_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{$this->getTable('feedexport/feed_product')}`;
CREATE TABLE {$this->getTable('feedexport/feed_product')} (
    `id`               int(11)      UNSIGNED NOT NULL auto_increment,
    `feed_id`          int(11)      NOT NULL,
    `product_id`       int(10)      UNSIGNED NOT NULL,
    PRIMARY KEY  (`id`),
    UNIQUE KEY `IDX_BASE` (`feed_id`, `product_id`),
    CONSTRAINT `FK_FEEDEXPORT_FEED_PRODUCT_FEED` FOREIGN KEY (`feed_id`) REFERENCES {$this->getTable('feedexport/feed')} (`feed_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_FEEDEXPORT_FEED_PRODUCT_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES {$this->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();