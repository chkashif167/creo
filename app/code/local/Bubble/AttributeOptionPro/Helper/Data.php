<?php
/**
 * @category    Bubble
 * @package     Bubble_AttributeOptionPro
 * @version     1.1.4
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_AttributeOptionPro_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getAttributeImage($attribute)
    {
        if ($attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
            $model = $attribute;
        } else {
            $entityTypeId = Mage::getModel('eav/entity')
                ->setType('catalog_product')
                ->getTypeId();
            $model = Mage::getResourceModel('catalog/eav_attribute')
                ->setEntityTypeId($entityTypeId);

            if (is_numeric($attribute)) {
                $model->load(intval($attribute));
            } else {
                $model->load($attribute, 'attribute_code');
            }
        }

        return $this->_getImageUrl($model->getImage());
    }

    public function getAttributeOptionImage($optionId)
    {
        $images = $this->getAttributeOptionImages();
        $image = array_key_exists($optionId, $images) ? $images[$optionId] : '';

        return $this->_getImageUrl($image);
    }

    public function getAttributeOptionImages()
    {
        $images = Mage::getResourceModel('eav/entity_attribute_option')->getAttributeOptionImages();

        return $images;
    }

    public function getAttributeOptionAdditionalImage($optionId)
    {
        $images = $this->getAttributeOptionAdditionalImages();
        $image = array_key_exists($optionId, $images) ? $images[$optionId] : '';

        return $this->_getImageUrl($image);
    }

    public function getAttributeOptionAdditionalImages()
    {
        $images = Mage::getResourceModel('eav/entity_attribute_option')->getAttributeOptionAdditionalImages();

        return $images;
    }

    public function isAdditionalImageEnabled()
    {
        return Mage::getStoreConfigFlag('bubble_aop/general/enable_additional_image');
    }

    public function getHiddenStores()
    {
        $storeIds = (string) Mage::getStoreConfig('bubble_aop/general/hide_stores');

        return !empty($storeIds) ? explode(',', $storeIds) : array();
    }

    public function isStoreHidden($storeId)
    {
        return in_array($storeId, $this->getHiddenStores());
    }

    protected function _getImageUrl($image)
    {
        if ($image && (strpos($image, 'http') !== 0)) {
            $image = Mage::getDesign()->getSkinUrl($image);
        }

        return $image;
    }
}