<?php
/**
 * @category    Bubble
 * @package     Bubble_AttributeOptionPro
 * @version     1.1.4
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('eav_attribute_option')}`
    ADD `image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    ADD `additional_image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
");

$installer->endSetup();
