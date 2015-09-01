<?php

class Mango_Attributeswatches_Helper_Product_List extends Mage_Core_Helper_Abstract {

    protected $_product;
    //protected $_processed_swatches = false;
    protected $_swatches_info = false;
    //protected $_processed_availability = false;
    protected $_availability_info = false;
    protected $_images_width = false;
    protected $_images_height = false;
    /* resize extra parameters */
    protected $_keep_aspect_ratio = true;
    protected $_constrain_only = true;
    protected $_keep_frame = true;
    protected $_settings = false;

    public function setProduct($product) {
        $this->_product = $product;
        //$this->_processed_swatches = false;
        //$this->_processed_availability = false;
        $this->_swatches_info = false;
        $this->_availability_info = false;
        return $this;
    }

    public function setDimensions($w, $h = false, $keep_aspect_ratio = false) {
        $this->_images_width = $w;
        $this->_images_height = $h;
        if ($keep_aspect_ratio) {
            $this->_keep_aspect_ratio = true;
            $this->_constrain_only = false;
            $this->_keep_frame = false;
        }
        return $this;
    }

    public function showAvailability() {
        if (!$this->_availability_info && $this->_product) {
            $_product = $this->_product;
            //    $time_start = microtime(true);
            //$_product = $this->getProduct();
            if (!$_product->isConfigurable())
                return false;
            $_availability_attribute = $this->getSettting("availability");
            $_availability_configuration = Mage::getResourceModel('attributeswatches/attributes')->hasConfigurableAttributeSimple($_availability_attribute, $_product->getId());
            $_availability_html = "";
            if (is_array($_availability_configuration) && count($_availability_configuration)) {
                //        <!--span class="attribute-availability-label"><?php //echo $_availability_configuration["label"];   </span-->
                $_availability_html = '<ul class="attribute-availability">';
                foreach ($_availability_configuration as $_i) {
                    $_availability_html.='<li>' . $_i["label"] . '</li>';
                }
                $_availability_html.='</ul>';
            }
//    $time_end = microtime(true);
//dividing with 60 will give the execution time in minutes other wise seconds
//    $execution_time = ($time_end - $time_start);
//execution time of the script
//    echo '<span style="display:none">' . $execution_time . ' s.</span>';

            $this->_availability_info = $_availability_html;
        }
        return $this->_availability_info;
//}
    }

    function processSwatches() {
        if (!$this->_swatches_info && $this->_product) {
            $_product = $this->_product;
//    $time_start = microtime(true);
            $_product_image = "";
            $_product_url = "";
            $_swatches_html = "";
            if ($_product->isConfigurable()) {
                //return false;
                $_configurable_attributes = $this->getSettting("attributes");
                $_configurable_attributes_layered = explode(",", $_configurable_attributes);
                //$_configurable_attributes = explode(",", $_configurable_attributes);
                $_configurable_attributes = str_replace(",", "','", $_configurable_attributes);
                /* to switch image and url based on layered navigation values */
                $_product_image = "";
                $_product_url = "";
                $_values_to_check = array();
                /* set product image based on selected layered navigation filter */
                if (Mage::getStoreConfig("attributeswatches/layerednavigation/switchimages")) {
                    $_configurable_attributes_switch = $this->getSettting("attributes");
                    $_configurable_attributes_switch = explode(",", $_configurable_attributes_switch);
                    /* get selected value based on attribute */
                    $_vals = array_intersect($_configurable_attributes_layered, $_configurable_attributes_switch);
                    foreach ($_vals as $_att) {
                        if (trim(Mage::app()->getRequest()->getParam($_att)))
                            $_values_to_check[$_att] = trim(Mage::app()->getRequest()->getParam($_att));
                    }
                }
                $_swatches = Mage::getResourceModel('attributeswatches/attributes')->hasConfigurableAttribute($_configurable_attributes, $_product->getId());
//    $time_end = microtime(true);
//dividing with 60 will give the execution time in minutes other wise seconds
//    $execution_time = ($time_end - $time_start);
//execution time of the script
//    echo '<span style="display:none">' . $execution_time . ' s.</span>';
                if ($_swatches) {
                    $_swatches_mode = $this->getSettting("mode");
                    $_image_source = $this->getSettting("images");
                    $_gallery_images = array();
                    /* to get the attribute id */
                    $_attribute_id = 0;
                    $_attribute_code = "";
                    foreach ($_swatches as $_p => $_data) {
                        $_attribute_id = $_data["attribute_id"];
                        $_attribute_code = $_data["attribute"];
                        break;
                    }
                    if ($_image_source == "gallery") {
//            $time_end = microtime(true);
//dividing with 60 will give the execution time in minutes other wise seconds
//            $execution_time = ($time_end - $time_start);
//execution time of the script
//            echo '<span style="display:none">' . $execution_time . ' s.</span>';
                        $_gallery = $_product->load($_product->getId())->getSwatchesGalleryImages($_attribute_id);
//            $time_end = microtime(true);
//dividing with 60 will give the execution time in minutes other wise seconds
//            $execution_time = ($time_end - $time_start);
//execution time of the script
//            echo '<span style="display:none">after loading gallery ' . $execution_time . ' s.</span>';
                        foreach ($_gallery as $_image) {
                            $_gallery_images[$_image->getAttributeValue()] = $_image->getFile();
                        }
                    }
                    /* loading products to be used for swatches */
                    //$_swatches_html = $_attribute_id . $_product->getId();
                    $_swatches_html = '<ul class="attribute-swatches product-list">';
                    $counter = 0;
                    foreach ($_swatches as $product_id => $_option) {
                        $counter++;
                        $_swatch = "";
                        $_thumbnail = "";
                        if ($_swatches_mode == "swatches") {
                            if ($_option["mode"] == 2) {
                                $_swatch = "background-color:#" . $_option["color"];
                            } elseif ($_option["mode"] == 1) {
                                $_swatch = "background-image:url('" . Mage::getBaseUrl('media') . 'attributeswatches/' . $_option["filename"] . "');background-size: 100% auto;background-repeat:no-repeat;";
                            } elseif ($_option["mode"] == 3) {
                                $_swatch = "background-image:url('" . Mage::getBaseUrl('media') . 'attributeswatches/' . $_option["filename"] . "');background-repeat:repeat;";
                            }
                        } elseif ($_swatches_mode == "image") {
                            if ($_image_source == "child") {
                                $product = Mage::getModel('catalog/product')->load($product_id);
                                $_swatch = "background-image:url('" . Mage::helper('catalog/image')->init($product, 'image')->resize((int) $this->getSettting("swatch_width"), (int) $this->getSettting("swatch_height")) . "');";
                            } elseif ($_image_source == "gallery") {
                                if (isset($_gallery_images[$_option["value"]]) && trim($_gallery_images[$_option["value"]])) {
                                    $_swatch = "background-image:url('" . Mage::helper('catalog/image')->init($_product, 'thumbnail', $_gallery_images[$_option["value"]])->resize((int) $this->getSettting("swatch_width"), (int) $this->getSettting("swatch_height")) . "');";
                                }
                            }
                        }
                        if ($_image_source == "child") {
                            $product = Mage::getModel('catalog/product')->load($product_id);
                            $_thumbnail = Mage::helper('catalog/image')->init($product, 'image')->keepAspectRatio($this->_keep_aspect_ratio)->constrainOnly($this->_constrain_only)->keepFrame($this->_keep_frame)->resize($this->_images_width, $this->_images_height);
                        } elseif ($_image_source == "gallery") {
                            if (isset($_gallery_images[$_option["value"]]) && trim($_gallery_images[$_option["value"]])) {
                                $_thumbnail = Mage::helper('catalog/image')->init($_product, 'thumbnail', $_gallery_images[$_option["value"]])->keepAspectRatio($this->_keep_aspect_ratio)->constrainOnly($this->_constrain_only)->keepFrame($this->_keep_frame)->resize($this->_images_width, $this->_images_height);
                            }
                        }
                        if (count($_values_to_check)) {
                            foreach ($_values_to_check as $_att => $_value) {
                                if ($_attribute_code == $_att && $_value == $_option["value"]) {
                                    $_product_image = (string) $_thumbnail;
                                    $_product_url = (string) $_product->getProductUrl() . "#" . $_attribute_id . '=' . $_option["value"]; //$_product->getUrlModel()->getUrl($_product, array("_fragment"=> $_attribute_id.'='. $_option["value"] ));
                                }
                            }
                        }
                        $_swatches_html.='<li class="attribute-swatch-' . $counter . '">';
                        $_swatches_html.='<a href="' . $_product->getProductUrl() . "#" . $_attribute_id . '=' . $_option["value"] . '" class="' . $_attribute_id . "-" . $_option["value"] . '" rel="' . $_thumbnail . '" style="' . $_swatch . ';width:' . (int) $this->getSettting("swatch_width") . 'px;height: ' . (int) $this->getSettting("swatch_height") . 'px;">';
                        //$_swatches_html.='<a data-toggle="tooltip" title="' . $_option["label"] . '" href="' . $_product->getProductUrl() . "#" . $_attribute_id . '=' . $_option["value"] . '" class="' . $_attribute_code . "-" . $_option["value"] . '" rel="' . $_thumbnail . '" style="' . $_swatch . '">';
                        $_swatches_html.='&nbsp;';
                        $_swatches_html.='</a>';
                        $_swatches_html.='<span class="tooltip-container"><span class="tooltip"><span>' . $_option["label"] . ' </span></span></span>';
                        $_swatches_html.='</li>';
                    }
                    $_swatches_html.='</ul>';
                }
                /* if no image is associated to the selected layered option, display the default image */
                if (!$_product_image) {
                    $_product_image = (string) Mage::helper('catalog/image')->init($_product, 'small_image')->keepAspectRatio($this->_keep_aspect_ratio)->constrainOnly($this->_constrain_only)->keepFrame($this->_keep_frame)->resize($this->_images_width, $this->_images_height);
                }
                if (!$_product_url) {
                    $_product_url = $_product->getProductUrl();
                }
//    $time_end = microtime(true);
//dividing with 60 will give the execution time in minutes other wise seconds
//    $execution_time = ($time_end - $time_start);
//execution time of the script
//    echo '<span style="display:none">' . $execution_time . ' s.</span>';
            } else {
                $_swatches_html = "";
                $_product_image = (string) Mage::helper('catalog/image')->init($_product, 'small_image')->keepAspectRatio($this->_keep_aspect_ratio)->constrainOnly($this->_constrain_only)->keepFrame($this->_keep_frame)->resize($this->_images_width, $this->_images_height);
                $_product_url = $_product->getProductUrl();
            }
            /* to add alternate image - display on image hover */
            $_alternate_image_source = trim($this->getSettting("alternate_image_source"));
            $_hover_image = false;
            if ($_alternate_image_source && $_product->getData($_alternate_image_source) && $_product->getData($_alternate_image_source) !== "no_selection") {
                $_hover_image = (string) Mage::helper('catalog/image')->init($_product, $_alternate_image_source)->keepAspectRatio($this->_keep_aspect_ratio)->constrainOnly($this->_constrain_only)->keepFrame($this->_keep_frame)->resize($this->_images_width, $this->_images_height);
            }
            $this->_swatches_info = array("swatches" => $_swatches_html, "product_image" => $_product_image, "product_url" => $_product_url, "hover_image" => $_hover_image);
        }
        return $this->_swatches_info;
    }

    public function getSwatches() {
        if (!$this->_swatches_info) {
            $this->processSwatches();
        }
        return $this->_swatches_info["swatches"];
    }

    public function getProductImage() {
        if (!$this->_swatches_info) {
            $this->processSwatches();
        }
        return $this->_swatches_info["product_image"];
    }

    public function getProductUrl() {
        if (!$this->_swatches_info) {
            $this->processSwatches();
        }
        return $this->_swatches_info["product_url"];
    }

    public function getHoverImage() {
        if (!$this->_swatches_info) {
            $this->processSwatches();
        }
        return $this->_swatches_info["hover_image"];
    }

    public function getSettting($_value) {
        if (!$this->_settings) {
            $this->_settings = Mage::getStoreConfig("attributeswatches/productlist");
        }
        if (isset($this->_settings[$_value]))
            return $this->_settings[$_value];
        return false;
    }

}
