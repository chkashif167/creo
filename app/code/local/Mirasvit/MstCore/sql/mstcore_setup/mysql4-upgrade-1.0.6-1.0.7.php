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
if ($version == '1.0.7') {
    return;
} elseif ($version != '1.0.6') {
    die("Please, run migration Mst_Core 1.0.6");
}

$installer = $this;
$installer->startSetup();

try {
    $installer->run("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('mstcore/urlrewrite')}` (
        `urlrewrite_id` int(11) NOT NULL AUTO_INCREMENT,
        `url_key` VARCHAR(255) NOT NULL DEFAULT '',
        `module` VARCHAR(255) NOT NULL DEFAULT '',
        `type` VARCHAR(255) NOT NULL DEFAULT '',
        `entity_id` INT(11),
        PRIMARY KEY (`urlrewrite_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
    CREATE UNIQUE INDEX mstcore_urlrewrite_index1 ON `{$this->getTable('mstcore/urlrewrite')}` (module, type, entity_id);
    CREATE UNIQUE INDEX mstcore_urlrewrite_index2 ON `{$this->getTable('mstcore/urlrewrite')}` (url_key, module);
    ");
} catch (Exception $e) {}

$installer->endSetup();
