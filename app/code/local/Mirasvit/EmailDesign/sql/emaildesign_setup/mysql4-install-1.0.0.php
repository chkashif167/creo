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
    DROP TABLE IF EXISTS `{$installer->getTable('emaildesign/design')}`;
    CREATE TABLE `{$installer->getTable('emaildesign/design')}` (
        `design_id`               int(11)      NOT NULL AUTO_INCREMENT,
        `title`                   varchar(255) NOT NULL,
        `description`             text         NULL,
        `template_type`           varchar(255) NOT NULL,
        `styles`                  text         NULL,
        `template`                text         NULL,

        `created_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`design_id`)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `{$installer->getTable('emaildesign/template')}`;
    CREATE TABLE `{$installer->getTable('emaildesign/template')}` (
        `template_id`             int(11)      NOT NULL AUTO_INCREMENT,
        `design_id`               int(11)      NULL,
        `title`                   varchar(255) NOT NULL,
        `description`             text         NULL,
        `subject`                 varchar(255) NOT NULL,
        `areas_content`           longtext     NULL,

        `created_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`              datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`template_id`)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8;
");

$installer->endSetup();

// populate data
$designPath   = Mage::getSingleton('emaildesign/config')->getDesignPath();
$templatePath = Mage::getSingleton('emaildesign/config')->getTemplatePath();

$ioFile = new Varien_Io_File();
$ioFile->open();
$ioFile->cd($designPath);

foreach ($ioFile->ls(Varien_Io_File::GREP_FILES) as $fl) {
    if ($fl['filetype'] == 'xml') {
        $design = Mage::getModel('emaildesign/design');
        $design->import($designPath.DS.$fl['text']);
    }
}

$ioFile->cd($templatePath);

foreach ($ioFile->ls(Varien_Io_File::GREP_FILES) as $fl) {
    if ($fl['filetype'] == 'xml') {
        $template = Mage::getModel('emaildesign/template');
        $template->import($templatePath.DS.$fl['text']);
    }
}