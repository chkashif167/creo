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
DROP TABLE IF EXISTS `{$this->getTable('feedexport/performance_click')}`;
CREATE TABLE `{$this->getTable('feedexport/performance_click')}` (
    `id`                 int(11)      NOT NULL AUTO_INCREMENT,
    `feed_id`            int(11)      NOT NULL,
    `session_id`         varchar(255) NOT NULL,
    `product_id`         int(10)      UNSIGNED NOT NULL,
    `store_id`           smallint(5)  UNSIGNED NOT NULL,
    `created_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    UNIQUE KEY `IDX_BASE` (`feed_id`, `session_id`, `product_id`, `store_id`),
    CONSTRAINT `FK_FEEDEXPORT_PERFORMANCE_CLICK_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_FEEDEXPORT_PERFORMANCE_CLICK_FEED` FOREIGN KEY (`feed_id`) REFERENCES `{$this->getTable('feedexport/feed')}` (`feed_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_FEEDEXPORT_PERFORMANCE_CLICK_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `{$this->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{$this->getTable('feedexport/performance_order')}`;
CREATE TABLE `{$this->getTable('feedexport/performance_order')}` (
    `id`                 int(11)        NOT NULL AUTO_INCREMENT,
    `feed_id`            int(11)        NOT NULL,
    `session_id`         varchar(255)   NOT NULL,
    `store_id`           smallint(5)    UNSIGNED NOT NULL,
    `product_id`         int(10)        UNSIGNED NOT NULL,
    `order_id`           int(10)        UNSIGNED NOT NULL,
    `price`              decimal(12,4)  NOT NULL,
    `created_at`         datetime       NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    UNIQUE KEY `IDX_BASE` (`feed_id`, `session_id`, `product_id`, `store_id`),
    CONSTRAINT `FK_FEEDEXPORT_PERFORMANCE_ORDER_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_FEEDEXPORT_PERFORMANCE_ORDER_FEED` FOREIGN KEY (`feed_id`) REFERENCES `{$this->getTable('feedexport/feed')}` (`feed_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_FEEDEXPORT_PERFORMANCE_ORDER_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `{$this->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_FEEDEXPORT_PERFORMANCE_ORDER_ORDER` FOREIGN KEY (`order_id`) REFERENCES `{$this->getTable('sales_flat_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{$this->getTable('feedexport/performance_aggregated')}`;
CREATE TABLE `{$this->getTable('feedexport/performance_aggregated')}` (
    `id`                 int(11)        NOT NULL AUTO_INCREMENT,
    `feed_id`            int(11)        NOT NULL,
    `product_id`         int(10)        UNSIGNED NOT NULL,
    `store_id`           smallint(5)    UNSIGNED NOT NULL,
    `period`             date           NOT NULL,
    `clicks`             int(11)        NULL,
    `orders`             int(11)        NULL,
    `revenue`            decimal(12,4)  NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `IDX_BASE` (`feed_id`, `product_id`, `store_id`, `period`),
    CONSTRAINT `FK_FEEDEXPORT_PERFORMANCE_AGGREGATED_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_FEEDEXPORT_PERFORMANCE_AGGREGATED_FEED` FOREIGN KEY (`feed_id`) REFERENCES `{$this->getTable('feedexport/feed')}` (`feed_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_FEEDEXPORT_PERFORMANCE_AGGREGATED_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `{$this->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();