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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product model
 *
 * @method Mage_Catalog_Model_Resource_Product getResource()
 * @method Mage_Catalog_Model_Resource_Product _getResource()
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Attributeswatches_Model_Catalog_Product extends Mage_Catalog_Model_Product {

    /**
     * Retrive media gallery images
     *
     * @return Varien_Data_Collection
     */
    public function getMediaGalleryImages() {
        if (!$this->hasData('media_gallery_images') && is_array($this->getMediaGallery('images'))) {
            $images = new Varien_Data_Collection();
            foreach ($this->getMediaGallery('images') as $image) {
                /* skip if image is not associated with attributes, will hide in the template with css */
                if ($image['disabled'] && !$image['associated_attributes']) {
                    continue;
                }
                $image['url'] = $this->getMediaConfig()->getMediaUrl($image['file']);
                $image['id'] = isset($image['value_id']) ? $image['value_id'] : null;
                $image['path'] = $this->getMediaConfig()->getMediaPath($image['file']);
                $images->addItem(new Varien_Object($image));
            }

            /* load images from child products in case the setting in the admin is set to true  */
            if ($this->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && Mage::getStoreConfig('attributeswatches/settings/images') == "child") {
                $_associated_attributes = array();
                $_associated_attributes_codes = array();
                $_atts = $this->getTypeInstance(true)->getConfigurableAttributes($this);
                if ($_atts) {
                    $_configurable_attributes_swatches = explode(",", Mage::getStoreConfig('attributeswatches/settings/switchimage') . "," . Mage::getStoreConfig('attributeswatches/productlist/attributes'));
                    foreach ($_atts as $attribute) {
                        $_code = $attribute->getProductAttribute()->getAttributeCode();
                        $_id = $attribute->getProductAttribute()->getAttributeId(); //AttributeCode();
                        if (in_array($_code, $_configurable_attributes_swatches)) {
                            $_associated_attributes[$_id] = $_code;
                            $_associated_attributes_codes[] = $_code;
                        }
                    }
                }

                if(count($_associated_attributes)){
                    $_associatedProducts = $this->getTypeInstance()->getUsedProducts();
                    $_classes_used = array();
                    foreach ($_associatedProducts as $_associatedProduct) {
                        if ($_associatedProduct->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
                            $_associatedProduct->load($_associatedProduct->getId());
                            if (!$_associatedProduct->hasData('media_gallery_images') && is_array($_associatedProduct->getMediaGallery('images'))) {
                                $_swatches_class = array();
                                foreach ($_associated_attributes as $_at_id => $_at_code) {
                                    $_swatches_class[] = "attribute" . $_at_id . "-" . $_associatedProduct->getData($_at_code);
                                }
                                $_swatches_class = implode(" ", $_swatches_class);
                                /* skip images when the attributes combination has been used already.. */
                                if(in_array($_swatches_class, $_classes_used)){
                                    continue;
                                }
                                $_classes_used[] = $_swatches_class;
                                foreach ($_associatedProduct->getMediaGallery('images') as $image) {
                                    //skip if image is not associated with attributes, will hide in the template with css
                                    if ($image['disabled']) {
                                        continue;
                                    }
                                    $image['url'] = $this->getMediaConfig()->getMediaUrl($image['file']);
                                    $image['id'] = isset($image['value_id']) ? $image['value_id'] : null;
                                    $image['path'] = $this->getMediaConfig()->getMediaPath($image['file']);
                                    $image['associated_attributes'] = $_swatches_class;
                                    $images->addItem(new Varien_Object($image));
                                }
                            }
                        }
                    }
                }
            }

            $this->setData('media_gallery_images', $images);
        }

        return $this->getData('media_gallery_images');
    }

    /**
     * Retrive media gallery images
     *
     * @return Varien_Data_Collection
     */
    public function getSwatchesGalleryImages($_attribute_id) {
        $_used_combinations = array();
        if (!$this->hasData('media_gallery_images_' . $_attribute_id)){
            $images = new Varien_Data_Collection();
            foreach ($this->getMediaGallery('images') as $image) {
                /* skip if image is not associated with attributes, will hide in the template with css */
                //echo $image['associated_attributes'];
                if (!$image['associated_attributes']) {
                    continue;
                }

                /* get the attribute associated and the value */
                $_attribute_values = array();
                $image['associated_attributes'] = str_replace("attribute", "", $image['associated_attributes']);
                $_attribute_values = explode(' ', $image['associated_attributes']);
                $_attribute_value = "";
                foreach ($_attribute_values as &$comb) {
                    $comb = explode('-', $comb);
                    /* find attribute value inside the string */
                    if (count($comb) == 2 && $comb[0] == $_attribute_id) {
                        $_attribute_value = $comb[1];
                        break;
                    }
                }
                unset($comb);

                if (in_array($_attribute_value, $_used_combinations)) {
                    continue;
                }
                /* use array to include only one gallery item per combination */
                $_used_combinations[] = $_attribute_value;


                $image['url'] = $this->getMediaConfig()->getMediaUrl($image['file']);
                $image['id'] = isset($image['value_id']) ? $image['value_id'] : null;
                $image['path'] = $this->getMediaConfig()->getMediaPath($image['file']);
                $image['attribute_value'] = $_attribute_value;
                $images->addItem(new Varien_Object($image));
            }
            $this->setData('swatches_gallery_images_' . $_attribute_id, $images);
        }

        return $this->getData('swatches_gallery_images_' . $_attribute_id);
    }

}
