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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



$installer = $this;

$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS `{$installer->getTable('searchlandingpage/page')}`;");
$installer->run("
CREATE TABLE `{$installer->getTable('searchlandingpage/page')}` (
   `page_id`          int(11)      unsigned NOT NULL auto_increment,
   `query_text`       varchar(255) NOT NULL default '',
   `url_key`          varchar(255) NOT NULL default '',
   `title`            varchar(255) NOT NULL default '',
   `meta_title`       varchar(255) NOT NULL default '',
   `meta_keywords`    varchar(255) NOT NULL default '',
   `meta_description` varchar(255) NOT NULL default '',
   `layout`           text         NULL,
   `is_active`        int(1)       NOT NULL default 0,
    PRIMARY KEY (`page_id`)
) ENGINE=InnoDb DEFAULT CHARSET=utf8;
");

$installer->endSetup();
