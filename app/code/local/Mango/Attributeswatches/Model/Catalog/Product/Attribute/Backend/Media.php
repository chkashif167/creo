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
 * Catalog product media gallery attribute backend model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Attributeswatches_Model_Catalog_Product_Attribute_Backend_Media extends Mage_Catalog_Model_Product_Attribute_Backend_Media {

    /**
     * Load attribute data after product loaded
     *
     * @param Mage_Catalog_Model_Product $object
     */
    public function afterLoad($object) {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = array();
        $value['images'] = array();
        $value['values'] = array();
        $localAttributes = array('label', 'position', 'disabled', 'associated_attributes');

        foreach ($this->_getResource()->loadGallery($object, $this) as $image) {
            foreach ($localAttributes as $localAttribute) {
                if ( !isset($image[$localAttribute]) || is_null($image[$localAttribute])) {
                    $image[$localAttribute] = $this->_getDefaultValue($localAttribute, $image);
                }
            }
            $value['images'][] = $image;
        }

        $object->setData($attrCode, $value);
    }

    public function beforeSave($object) {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!is_array($value) || !isset($value['images'])) {
            return;
        }

        if (!is_array($value['images']) && strlen($value['images']) > 0) {
            $value['images'] = Mage::helper('core')->jsonDecode($value['images']);
        }

        if (!is_array($value['images'])) {
            $value['images'] = array();
        }



        $clearImages = array();
        $newImages = array();
        $existImages = array();
        if ($object->getIsDuplicate() != true) {
            foreach ($value['images'] as &$image) {
                if (!empty($image['removed'])) {
                    $clearImages[] = $image['file'];
                } else if (!isset($image['value_id'])) {
                    $newFile = $this->_moveImageFromTmp($image['file']);
                    $image['new_file'] = $newFile;
                    $newImages[$image['file']] = $image;
                    $this->_renamedImages[$image['file']] = $newFile;
                    $image['file'] = $newFile;
                } else {
                    $existImages[$image['file']] = $image;
                }
            }
        } else {
            // For duplicating we need copy original images.
            $duplicate = array();
            foreach ($value['images'] as &$image) {
                if (!isset($image['value_id'])) {
                    continue;
                }
                $duplicate[$image['value_id']] = $this->_copyImage($image['file']);
                $newImages[$image['file']] = $duplicate[$image['value_id']];
            }

            $value['duplicate'] = $duplicate;
        }

        foreach ($object->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $attrData = $object->getData($mediaAttrCode);

            if (in_array($attrData, $clearImages)) {
                $object->setData($mediaAttrCode, 'no_selection');
            }

            if (in_array($attrData, array_keys($newImages))) {
                $object->setData($mediaAttrCode, $newImages[$attrData]['new_file']);
                $object->setData($mediaAttrCode . '_label', $newImages[$attrData]['label']);
                $object->setData($mediaAttrCode . '_associated_attributes', $newImages[$attrData]['associated_attributes']);
            }

            if (in_array($attrData, array_keys($existImages))) {
                $object->setData($mediaAttrCode . '_label', $existImages[$attrData]['label']);
                $object->setData($mediaAttrCode . '_associated_attributes', $existImages[$attrData]['associated_attributes']);
            }
        }

        Mage::dispatchEvent('catalog_product_media_save_before', array('product' => $object, 'images' => $value));

        $object->setData($attrCode, $value);

        return $this;
    }

    public function afterSave($object) {
        if ($object->getIsDuplicate() == true) {
            $this->duplicate($object);
            return;
        }

        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!is_array($value) || !isset($value['images']) || $object->isLockedAttribute($attrCode)) {
            return;
        }

        $storeId = $object->getStoreId();

        $storeIds = $object->getStoreIds();
        $storeIds[] = Mage_Core_Model_App::ADMIN_STORE_ID;

        // remove current storeId
        $storeIds = array_flip($storeIds);
        unset($storeIds[$storeId]);
        $storeIds = array_keys($storeIds);

        $images = Mage::getResourceModel('catalog/product')
                ->getAssignedImages($object, $storeIds);

        $picturesInOtherStores = array();
        foreach ($images as $image) {
            $picturesInOtherStores[$image['filepath']] = true;
        }

        $toDelete = array();
        $filesToValueIds = array();
        foreach ($value['images'] as &$image) {
            if (!empty($image['removed'])) {
                if (isset($image['value_id']) && !isset($picturesInOtherStores[$image['file']])) {
                    $toDelete[] = $image['value_id'];
                }
                continue;
            }

            if (!isset($image['value_id'])) {
                $data = array();
                $data['entity_id'] = $object->getId();
                $data['attribute_id'] = $this->getAttribute()->getId();
                $data['value'] = $image['file'];
                $image['value_id'] = $this->_getResource()->insertGallery($data);
            }

            $this->_getResource()->deleteGalleryValueInStore($image['value_id'], $object->getStoreId());

            // Add per store labels, position, disabled
            $data = array();
            $data['value_id'] = $image['value_id'];
            $data['label'] = $image['label'];
            //added by Mango
            $data['associated_attributes'] = $image['associated_attributes'];
            $data['position'] = (int) $image['position'];
            $data['disabled'] = (int) $image['disabled'];
            $data['store_id'] = (int) $object->getStoreId();

            $this->_getResource()->insertGalleryValueInStore($data);
        }

        $this->_getResource()->deleteGallery($toDelete);
    }

    /**
     * Update image in gallery
     *
     * @param Mage_Catalog_Model_Product $product
     * @param sting $file
     * @param array $data
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Media
     */
    public function updateImage(Mage_Catalog_Model_Product $product, $file, $data) {
        $fieldsMap = array(
            'label' => 'label',
            'position' => 'position',
            'associated_attributes' => 'associated_attributes',
            'disabled' => 'disabled',
            'exclude' => 'disabled'
        );

        $attrCode = $this->getAttribute()->getAttributeCode();

        $mediaGalleryData = $product->getData($attrCode);

        if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
            return $this;
        }

        foreach ($mediaGalleryData['images'] as &$image) {
            if ($image['file'] == $file) {
                foreach ($fieldsMap as $mappedField => $realField) {
                    if (isset($data[$mappedField])) {
                        $image[$realField] = $data[$mappedField];
                    }
                }
            }
        }

        $product->setData($attrCode, $mediaGalleryData);
        return $this;
    }

}

// Class Mage_Catalog_Model_Product_Attribute_Backend_Media End
