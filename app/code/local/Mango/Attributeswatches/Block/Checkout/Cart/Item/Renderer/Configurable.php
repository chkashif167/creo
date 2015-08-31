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
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart item render block
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Attributeswatches_Block_Checkout_Cart_Item_Renderer_Configurable extends Mage_Checkout_Block_Cart_Item_Renderer_Configurable {

    /**
     * Get product thumbnail image
     *
     * @return Mage_Catalog_Model_Product_Image
     */
    public function getProductThumbnail() {
        if (Mage::getStoreConfig("attributeswatches/checkout/overrideimage")) {
            $_atts = explode(",", Mage::getStoreConfig("attributeswatches/settings/switchimage"));
            if(!count($_atts)) parent::getProductThumbnail();
            /* get combinations attribute{attributeid}-{value} */
            $product_instance = $this->getProduct()->getTypeInstance(true);
            $attributesOption = $product_instance->getProduct($this->getProduct())->getCustomOption('attributes');
            $_values = unserialize($attributesOption->getValue());
            $usedProductAttributesData = array();
            foreach ($product_instance->getConfigurableAttributes($this->getProduct()) as $attribute) {
                if (!is_null($attribute->getProductAttribute()) && isset($_values[$attribute->getProductAttribute()->getId()]) && in_array($attribute->getProductAttribute()->getAttributeCode(), $_atts)) {
                    $id = $attribute->getProductAttribute()->getId();
                    $usedProductAttributesData[$attribute->getProductAttribute()->getAttributeCode()] = "attribute" . $id . "-" . $_values[$id];
                }
            }
            $_images = Mage::getResourceSingleton('catalog/product_attribute_backend_media')->loadCartImage($this->getProduct(), $usedProductAttributesData);
            if(!count($_images))return parent::getProductThumbnail();
            return $this->helper('catalog/image')->init($this->getProduct(), 'thumbnail', $_images[0]["file"]);
        } else {

            return parent::getProductThumbnail();
        }
    }

}
