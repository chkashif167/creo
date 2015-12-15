<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Block_Adminhtml_Sales_Order_Grid_Renderer_Products extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        /** @var MageWorx_OrdersGrid_Helper_Data $helper */
        $helper = Mage::helper('mageworx_ordersgrid');

        // poducts or SKUs $prefix
        if ($this->getColumn()->getIndex() == 'product_names') {
            $prefix = 'p';
        } elseif ($this->getColumn()->getIndex() == 'skus') {
            $prefix = 's';
        } elseif ($this->getColumn()->getIndex() == 'product_options') {
            $prefix = 'o';
        } else {
            $prefix = 'a';
        }

        if ($prefix == 'o') {
            $arr = false;
            // parse product option(s)
            $products = explode('^', $row->getData($this->getColumn()->getIndex()));
            foreach ($products as $key => $product) {
                if (strlen($product) > 10) {
                    $arr = @unserialize($product);
                }
                if (!$arr) {
                    $arr = @unserialize(str_replace("\n", "\r\n", $product));
                }
                $product = $arr;
                if (isset($product['options']) && is_array($product['options'])) {
                    // custom options
                    $options = array();
                    foreach ($product['options'] as $option) {
                        $options[] = $option['label'] . ': ' . $option['value'];
                    }
                    $products[$key] = implode(', ', $options);
                } elseif (isset($product['bundle_options']) && is_array($product['bundle_options'])) {
                    // bundle
                    $options = array();
                    foreach ($product['bundle_options'] as $option) {
                        if (is_array($option['value'])) {
                            $values = array();
                            foreach ($option['value'] as $value) {
                                $values[] = $value['title'];
                            }
                            $options[] = $option['label'] . ': ' . implode(', ', $values);
                        } else {
                            $options[] = $option['label'] . ': ' . $option['value'];
                        }
                    }
                    $products[$key] = implode(', ', $options);
                } elseif (isset($product['simple_name']) && $product['simple_name']) {
                    // configurable
                    $products[$key] = $product['simple_name'];
                } elseif ($product === false) {
                    $products[$key] = 'Required value of MySQL setting: group_concat_max_len = 32768';
                } else {
                    $products[$key] = '';
                }
            }
        } else {
            $products = explode("\n", $this->htmlEscape($row->getData($this->getColumn()->getIndex())));
        }

        if (strpos(Mage::app()->getRequest()->getRequestString(), '/exportCsv/')) {
            return implode('|', $products);
        }

        // add Thumbnails
        $divFlag = false;
        if ($helper->isShowThumbnails() && $prefix == 'p') {
            $productIds = explode("\n", $row->getData('product_ids'));
            $imgSize = intval($helper->getThumbnailHeight());
            $productSimpleIds = explode("\n", $row->getData('skus'));
            $i = 0;
            foreach ($products as $key => $value) {
                if (isset($productIds[$key]) && $productIds[$key]) {
                    $product = Mage::getModel('catalog/product')->setStoreId($row->getStoreId())->load($productIds[$key]);
                    if ($product->getTypeId() == 'configurable') {
                        if (!isset($productSimpleIds[$i])) {
                            continue;
                        }
                        $simpleProduct = Mage::getModel('catalog/product')->setStoreId($row->getStoreId())->loadByAttribute('sku', $productSimpleIds[$i]);
                        if ($simpleProduct && $simpleProduct->getThumbnail() && $simpleProduct->getThumbnail() != 'no_selection') {
                            $product = $simpleProduct;
                        }
                    }
                    if ($product && $product->getThumbnail() && $product->getThumbnail() != 'no_selection') {
                        try {
                            $imgUrl = $this->helper('catalog/image')->init($product, 'thumbnail')->resize($imgSize, $imgSize);
                        } catch (Exception $e) {
                            $imgUrl = Mage::getDesign()->getSkinUrl('images/placeholder/thumbnail.jpg');
                        }
                    } else {
                        $imgUrl = Mage::getDesign()->getSkinUrl('images/placeholder/thumbnail.jpg');
                    }
                    $products[$key] = '<div style="height:auto;overflow:auto;"><img src="' . $imgUrl . '" height="' . $imgSize . '" width="' . $imgSize . '" alt="" align="left" style="padding-right:2px;" /><p>' . $products[$key] . '</p></div>';
                }
                $i++;
            }
            $divFlag = true;
        }

        $prCount = count($products);
        if ($prCount > 3) {
            $products[$prCount - 1] .= '<a href="" onclick="$(\'hdiv_' . $row->getData('increment_id') . '_' . $prefix . '\').style.display=\'none\'; $(\'a_' . $row->getData('increment_id') . '_' . $prefix . '\').style.display=\'block\'; return false;" style="float:right; font-weight:bold; text-decoration: none;" title="' . $helper->__('Less..') . '">↑</a>'
                . '</div>'
                . '<a href="" id="a_' . $row->getData('increment_id') . '_' . $prefix . '" onclick="$(\'hdiv_' . $row->getData('increment_id') . '_' . $prefix . '\').style.display=\'block\'; this.style.display=\'none\'; return false;" style="float:right; font-weight:bold; text-decoration: none;" title="' . $helper->__('More..') . '">↓</a>';
            $products[2] .= '<div id="hdiv_' . $row->getData('increment_id') . '_' . $prefix . '" style="display:none">' . $products[3];
            unset($products[3]);
        }
        return '<div style="cursor: text">' . implode(($divFlag ? "\n" : '<br/>'), $products) . '</div><a/>';
    }
}
