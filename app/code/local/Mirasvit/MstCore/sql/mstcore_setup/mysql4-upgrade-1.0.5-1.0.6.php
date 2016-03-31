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


$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('mstcore');
if ($version == '1.0.6') {
    return;
}

$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('mstcore/logger')} (
    `log_id`     int(11) NOT NULL auto_increment,
    `level`      int(11) NOT NULL,
    `message`    varchar(255) NOT NULL,
    `content`    text NOT NULL,
    `trace`      text NOT NULL,
    `module`     varchar(255) NOT NULL,
    `class`      varchar(255) NOT NULL,
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    PRIMARY KEY  (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
