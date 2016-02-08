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



//2.2.8 - 2.2.9
$installer = $this;

$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS `{$installer->getTable('searchindex/index')}`;");
$installer->run("
CREATE TABLE `{$installer->getTable('searchindex/index')}` (
   `index_id`              int(11) NOT NULL AUTO_INCREMENT,
   `index_code`            varchar(255) NOT NULL,
   `title`                 varchar(255) NOT NULL default '',
   `position`              int(11)      NOT NULL default 0,
   `attributes_serialized` text         NULL default '',
   `properties_serialized` text         NULL default '',
   `status`                int(1)       NOT NULL default 1,
   `is_active`             int(1)       NOT NULL default 0,
   `updated_at`            datetime     NULL,
    PRIMARY KEY (`index_id`)
) ENGINE=InnoDb DEFAULT CHARSET=utf8;
");

Mage::app()->getStore()->resetConfig();

$indexes = array(
   'catalog' => 'mage_catalog_product',
   'category' => 'mage_catalog_category',
   'cms' => 'mage_cms_page',
   'awblog' => 'aw_blog_post',
   'maction' => 'mirasvit_action_action',
   'wordpress' => 'external_wordpress_post',
);

foreach ($indexes as $oldCode => $newCode) {
    $path = 'searchindex/'.$oldCode.'/';

    $enabled = Mage::getStoreConfig($path.'enabled');
    $title = Mage::getStoreConfig($path.'title');
    $position = Mage::getStoreConfig($path.'position');

    $oldAttrs = Mage::getStoreConfig($path.'attribute');
    $oldAttrs = unserialize($oldAttrs);
    $newAttrs = array();
    if (is_array($oldAttrs)) {
        foreach ($oldAttrs as $item) {
            $newAttrs[$item['attribute']] = $item['value'];
        }
    }

    if ($oldCode == 'catalog') {
        $enabled = 1;
    }

    if ($enabled && $title) {
        $collection = Mage::getModel('searchindex/index')->getCollection()
         ->addFieldToFilter('index_code', $newCode);

        if ($collection->count()) {
            $index = $collection->getFirstItem();
        } else {
            $index = Mage::getModel('searchindex/index');
        }

        $index->setIndexCode($newCode)
         ->setTitle($title)
         ->setPosition($position)
         ->setStatus(3)
         ->setIsActive(1)
         ->setAttributes($newAttrs)
         ->save();
    }
}

$installer->endSetup();
