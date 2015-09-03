<?php
/**
 * FeaturedProduct Setup class
 * 
 * @category    Clarion
 * @package     Clarion_FeaturedProduct
 * @author      Clarion Magento Team <magento@clariontechnologies.co.in>
 * 
 */
$installer = $this;
/* @var $installer Clarion_FeaturedProduct_Model_Resource_Setup */
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_featured_product', array(
    'group'             => 'General',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Is Featured Product',
    'input'             => 'boolean',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'default'           => 0,
    'user_defined'      => true,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 0,   //0 For All Product Types
    'is_configurable'   => false,
));