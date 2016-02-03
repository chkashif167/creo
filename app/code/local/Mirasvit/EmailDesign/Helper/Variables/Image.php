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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailDesign_Helper_Variables_Image
{
    public function getImageUrl($parent, $args)
    {
        return $this->_getImageUrl('image', $args, $parent);   
    }

    public function getThumbnailUrl($parent, $args)
    {
        return $this->_getImageUrl('thumbnail', $args, $parent);   
    }

    public function getSmallImageUrl($parent, $args)
    {
        return $this->_getImageUrl('small_image', $args, $parent);   
    }

    protected function _getImageUrl($type, $args, $parent)
    {
        if (isset($args[0]) && is_object($args[0]) && $args[0] instanceof Mage_Catalog_Model_Product) {
            $product = $args[0];
            $product = Mage::getModel('catalog/product')->load($product->getId());
            if ($parent->getStore()) {
                $query = array(
                    'path' => $product->getData($type),
                    'size' => isset($args[1]) ? intval($args[1]) : 0,
                );

                return $parent->getStore()->getUrl('eml/index/image', array('_query' => $query));
            } else {
                $url = Mage::helper('catalog/image')
                    ->init($product, $type);

                if (isset($args[1]) && intval($args[1]) > 0) {
                    $url = $url->resize(intval($args[1]));
                }
                
                return $url->__toString();
            }
        }
    }
}
?>