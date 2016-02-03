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



//2.2.9 - 2.2.9.1
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
try {
    $setup->addAttribute('catalog_category', 'searchindex_weight', array(
        'group' => 'General Information',
        'input' => 'text',
        'type' => 'text',
        'label' => 'Search Weight',
        'backend' => '',
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    ));

    $setup->addAttribute('catalog_product', 'searchindex_weight', array(
        'group' => 'General',
        'input' => 'text',
        'type' => 'text',
        'label' => 'Search Weight',
        'backend' => '',
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    ));
} catch (Exception $e) {
    Mage::logException($e);
}
$installer->endSetup();
