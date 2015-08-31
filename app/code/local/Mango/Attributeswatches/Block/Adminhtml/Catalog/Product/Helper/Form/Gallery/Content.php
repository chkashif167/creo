<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product form gallery content
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Attributeswatches_Block_Adminhtml_Catalog_Product_Helper_Form_Gallery_Content extends Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('attributeswatches/catalog/product/helper/gallery.phtml');
    }

    public function getAttributes() {

        //echo "get attributes";
        
        //print_r($this->getElement()->toArray());
        //();
        //exit;
        $_associated_attributes = array();
        $_associated_attributes_codes = array();
        $_p = Mage::registry('current_product');

        if ($_p->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
            return false;


        //if ($_p->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
        $_atts = $_p->getTypeInstance(true)->getConfigurableAttributes($_p);
        if ($_atts) {
            $_configurable_attributes_swatches = explode(",", Mage::getStoreConfig('attributeswatches/settings/switchimage') . "," . Mage::getStoreConfig('attributeswatches/productlist/attributes') );
            //print_r($_configurable_attributes_swatches);
            foreach ($_atts as $attribute) {
                //print_r($attribute->getProductAttribute());
                //exit;
                $_code = $attribute->getProductAttribute()->getAttributeCode();
                $_id = $attribute->getProductAttribute()->getAttributeId();//AttributeCode();
                if (in_array($_code, $_configurable_attributes_swatches)) {
                    $_associated_attributes[$_id] = $_code;
                    $_associated_attributes_codes[] = $_code;
                }
            }
        }
        //}

        //print_r($_associated_attributes);
        
        if (!count($_associated_attributes))
            return false;


        $allowProductTypes = array();
        foreach (Mage::helper('catalog/product_configuration')->getConfigurableAllowedTypes() as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $product = $_p;
        $collection = $product->getTypeInstance(true)->getUsedProductCollection($product)
                ->addFieldToFilter('attribute_set_id', $product->getAttributeSetId())
                ->addFieldToFilter('type_id', $allowProductTypes)
                ->addFilterByRequiredOptions();
        
        foreach ($product->getTypeInstance(true)->getUsedProductAttributes($product) as $attribute) {
            if (in_array($attribute->getAttributeCode(), $_associated_attributes_codes)) {
                //echo $attribute->getAttributeCode() . "<br/>";
                $collection->addAttributeToSelect($attribute->getAttributeCode());
                $collection->addAttributeToFilter($attribute->getAttributeCode(), array('notnull' => 1));
            }
        }

        $_combinations = array();
        foreach ($collection as $_product) {
            
            $_val = array();
            $_label = array();
            foreach ($_associated_attributes as $_attribute_id=>$_attribute_code) {
                $_val[$_attribute_code] =  "attribute" . $_attribute_id . "-" . $_product->getData($_attribute_code);
                $_label[$_attribute_code] =   $_product->getResource()->getAttribute($_attribute_code)->getFrontend()->getValue($_product);
            }

            $_combinations[join(" ", $_val)] = join(" - ", $_label);

            //echo $_product->getId() . "-" . $_product->getColor() . "<br/>";


            
        }

        return $_combinations;

        
    }

}

