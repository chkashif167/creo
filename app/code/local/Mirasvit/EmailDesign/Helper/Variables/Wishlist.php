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



class Mirasvit_EmailDesign_Helper_Variables_Wishlist
{
    /**
     * Retrieve customer wishlist.
     *
     * @param $parent
     * @param $args
     *
     * @return bool|Mage_Core_Model_Abstract|Mage_Wishlist_Model_Wishlist
     */
    public function getWishlist($parent, $args)
    {
        $wishlist = Mage::getModel('wishlist/wishlist');
        if ($parent->getData('wishlist')) {
            return $parent->getData('wishlist');
        } elseif ($parent->getData('wishlist_id')) {
            $wishlist = $wishlist->load($parent->getData('wishlist_id'));
        } elseif ($parent->getData('customer_id')) {
            $wishlist = $wishlist->loadByCustomer($parent->getData('customer_id'));
        }
        $parent->setData('wishlist', $wishlist);

        return $wishlist;
    }

    public function getWishlistItemCollection($parent, $args)
    {
        $itemCollection = new Varien_Data_Collection();
        if ($parent->getData('wishlist_item_collection')) {
            return $parent->getData('wishlist');
        } else {
            $wishlist = $this->getWishlist($parent, $args);
            $itemCollection = Mage::getResourceModel('wishlist/item_collection')
                ->addWishlistFilter($wishlist);
            $parent->setData('wishlist_item_collection', $itemCollection);
        }

        return $itemCollection;
    }

    public function getWishlistProduct($parent, $args)
    {
        $product = Mage::getModel('catalog/product');
        if ($parent->getData('wishlist_product')) {
            return $parent->getData('wishlist_product');
        } elseif ($parent->getData('product_id')) {
            $product = $product->load($parent->getData('product_id'));
        } elseif ($wishlist = $this->getWishlist($parent, $args)) {
            $productId = $wishlist->getItemCollection()
                ->setOrder('added_at', 'desc')
                ->getFirstItem()->getProductId();
            $product = $product->load($productId);
        }
        $parent->setData('wishlist_product', $product);

        return $product;
    }
}
