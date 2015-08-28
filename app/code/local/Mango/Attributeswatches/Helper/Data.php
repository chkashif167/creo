<?php

class Mango_Attributeswatches_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getAttributesWithSwatches() {
        $_att = array();
        $_image_swatches = Mage::getStoreConfig('attributeswatches/settings/attributes', Mage::app()->getStore());
        $_image_swatches = explode(",", $_image_swatches);
        foreach ($_image_swatches as $x) {
            $_att[$x] = "image";
        }
        $_label_swatches = Mage::getStoreConfig('attributeswatches/settings/labels', Mage::app()->getStore());
        $_label_swatches = explode(",", $_label_swatches);
        foreach ($_label_swatches as $x) {
            $_att[$x] = "label";
        }
        return $_att;
    }

    public function getAttributesWithSwatchesProductView() {
        $_att = array();
        $_image_swatches = Mage::getStoreConfig('attributeswatches/settings/attributes', Mage::app()->getStore());
        $_image_swatches = explode(",", $_image_swatches);
        foreach ($_image_swatches as $x) {
            $_att[$x] = "image";
        }
        $_label_swatches = Mage::getStoreConfig('attributeswatches/settings/labels', Mage::app()->getStore());
        $_label_swatches = explode(",", $_label_swatches);
        foreach ($_label_swatches as $x) {
            $_att[$x] = "label";
        }
        $_child_swatches = Mage::getStoreConfig('attributeswatches/settings/childproducts', Mage::app()->getStore());
        $_child_swatches = explode(",", $_child_swatches);
        foreach ($_child_swatches as $x) {
            $_att[$x] = "child";
        }
        return $_att;
    }

    public function getAttributesProductViewHideSelect() {
        $_att = array();
        $_hideselect = Mage::getStoreConfig('attributeswatches/settings/hideselect', Mage::app()->getStore());
        $_hideselect = explode(",", $_hideselect);
        foreach ($_hideselect as $x) {
            $_att[$x] = "hideselect";
        }

        return $_att;
    }

    public function getAttributesSwitchGalleryProductView() {
        $_att = array();
        $_atts = Mage::getStoreConfig('attributeswatches/settings/switchimage', Mage::app()->getStore());
        $_atts = explode(",", $_atts);
        foreach ($_atts as $x) {
            $_att[$x] = "switchgallery";
        }
        return $_att;
    }

    public function getAttributesWithSwatchesList() {
        return Mage::getStoreConfig('attributeswatches/settings/list', Mage::app()->getStore());
    }

    /* public function getAttributesWithImagesSwatches(){
      $_image_swatches = Mage::getStoreConfig('attributeswatches/settings/images_swatches'  );
      $_image_swatches = explode(",", $_image_swatches);
      if(count($_image_swatches))return $_image_swatches;
      return array();
      } */

    public function getAttributesWithSwatchesForSQL() {
        $_swatches = Mage::getStoreConfig("attributeswatches/settings/attributes")
                . "," . Mage::getStoreConfig("attributeswatches/productlist/attributes")
                . "," . Mage::getStoreConfig("attributeswatches/layerednavigation/attributes")
                . ", " . Mage::getStoreConfig("attributeswatches/layerednavigation/hidelabel");
        //$_swatches = array_unique(explode(",", $_swatches));
        //print_r($_swatches);
        //exit;
        $_swatches = array_unique(array_map('trim', explode(',', $_swatches)));


        if (count($_swatches))
            return "'" . join("','", $_swatches) . "'";
        return false;
    }

    /* function to add swatches information to the configurable product options block */

    public function addAttributeSwatches(&$_info) {
        $_info = json_decode($_info, true);
        //json_decode( $_info, true);
        /* will get all the attributes with swatches  */
        $_attributes_with_swatches = $this->getAttributesWithSwatchesProductView();
        $_attributes_hideselect = $this->getAttributesProductViewHideSelect();
        $_attributes_switchgallery = $this->getAttributesSwitchGalleryProductView();
        /* hide select only if the attribute has another type of selector associated */
        foreach ($_attributes_hideselect as $_id => $type) {
            if (!isset($_attributes_with_swatches[$_id])) {
                unset($_attributes_hideselect[$_id]);
            }
        }
        $_swatches_ids = array();
        foreach ($_info['attributes'] as $_id => $_attribute) {
            /* options with swatches from the db */
            if (isset($_attributes_with_swatches[$_attribute["code"]]) && $_attributes_with_swatches[$_attribute["code"]] == "image") {
                foreach ($_attribute["options"] as $_option) {
                    $_swatches_ids[] = $_option["id"];
                }
            }
            /* set the swatch type to display in frontend */
            if (isset($_attributes_with_swatches[$_attribute["code"]])) {
                $_info['attributes'][$_id]["swatch_type"] = $_attributes_with_swatches[$_attribute["code"]];
            } else {
                $_info['attributes'][$_id]["swatch_type"] = false;
            }
            /* hide/show select in frontend */
            $_info['attributes'][$_id]["hideselect"] = isset($_attributes_hideselect[$_attribute["code"]]);
            /* switch gallery when attribute is selected */
            $_info['attributes'][$_id]["switchgallery"] = isset($_attributes_switchgallery[$_attribute["code"]]);
        }
        $_options = Mage::getModel('attributeswatches/attributeswatches')->getCollection()->addFieldToFilter('main_table.option_id', array('in' => $_swatches_ids));
        $_swatches_values = array();
        foreach ($_options as $_option) {
            $_swatch = "";
            if ($_option->getMode() == 2) {
                $_swatch = "background-color:#" . $_option->getColor();
            } elseif ($_option->getMode() == 1) {
                $_swatch = "background-image:url('" . Mage::getBaseUrl('media') . 'attributeswatches/' . $_option->getFilename() . "');";
            }
            $_swatches_values[$_option->getOptionId()] = $_swatch;
        }
        /* assign the images or colors to the swatches */
        foreach ($_info['attributes'] as $_id => $_attribute) {
            if (isset($_attributes_with_swatches[$_attribute["code"]]) && $_attributes_with_swatches[$_attribute["code"]] == "image") {
                foreach ($_attribute["options"] as $_i => $_option) {
                    if (isset($_swatches_values[$_option["id"]]))
                        $_info['attributes'][$_id]["options"][$_i]["swatch_value"] = $_swatches_values[$_option["id"]];
                }
            }
        }
        //print_r($_info);
        return $_info;
    }

    public function getWishlistImage($_wishlist_item, $_width, $_heigth) {
        $product = $_wishlist_item->getProduct();
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $_images = array();
            $_options = $product->getCustomOption('attributes');
            if($_options){
                $_values = unserialize($_options->getValue());
                $product_instance = $product->getTypeInstance(true);
                $_atts = explode(",", Mage::getStoreConfig("attributeswatches/settings/switchimage"));
                $usedProductAttributesData = array();
                foreach ($product_instance->getConfigurableAttributes($product) as $attribute) {
                    if (!is_null($attribute->getProductAttribute()) && in_array($attribute->getProductAttribute()->getAttributeCode(), $_atts) && isset($_values[$attribute->getProductAttribute()->getId()])) {
                        $id = $attribute->getProductAttribute()->getId();
                        $usedProductAttributesData[$attribute->getProductAttribute()->getAttributeCode()] = "attribute" . $id . "-" . $_values[$id];
                    }
                }
                $_images = Mage::getResourceSingleton('catalog/product_attribute_backend_media')->loadCartImage($product, $usedProductAttributesData);
            }
            if (!count($_images)) {
                $_img = Mage::helper('catalog/image')->init($product, 'thumbnail')->resize($_width, $_heigth);
            } else {
                $_img = Mage::helper('catalog/image')->init($product, 'thumbnail', $_images[0]["file"])->resize($_width, $_heigth);
            }
        } else {
            $_img = Mage::helper('catalog/image')->init($product, 'small_image')->resize($_width, $_heigth);
        }

        return $_img;
    }

}