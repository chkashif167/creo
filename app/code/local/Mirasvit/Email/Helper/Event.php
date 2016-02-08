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



class Mirasvit_Email_Helper_Event extends Mage_Core_Helper_Abstract
{
    public function getEventModel($eventCode)
    {
        $arr = explode('|', $eventCode);

        try {
            $model = Mage::getModel('email/event_'.$arr[0]);
        } catch (Exception $e) {
            Mage::logException($e);
            $model = false;
        }

        return $model;
    }

    public function getEventCodes()
    {
        $events = Mage::getSingleton('email/system_source_event')->toArray();

        $result = array();
        foreach ($events as $group => $sub) {
            foreach (array_keys($sub) as $value) {
                $result[] = $value;
            }
        }

        return $result;
    }

    public function getRandomEventArgs()
    {
        $customerCollection = Mage::getModel('customer/customer')->getCollection();
        $customerCollection->getSelect()->limit(1, rand(0, $customerCollection->getSize() - 1));
        $customer = Mage::getModel('customer/customer')->load($customerCollection->getFirstItem()->getId());

        $quoteCollection = Mage::getModel('sales/quote')->getCollection()
            ->addFieldToFilter('items_qty', array('gt' => 0));
        $quoteCollection->getSelect()->limit(1, rand(0, $quoteCollection->getSize() - 1));
        $quote = Mage::getModel('sales/quote')->setSharedStoreIds(array_keys(Mage::app()->getStores()))
            ->load($quoteCollection->getFirstItem()->getId());

        $orderCollection = Mage::getModel('sales/order')->getCollection();
        $orderCollection->getSelect()->limit(1, rand(0, $orderCollection->getSize() - 1));
        $order = Mage::getModel('sales/order')->load($orderCollection->getFirstItem()->getId());

        $wishlistCollection = Mage::getModel('wishlist/wishlist')->getCollection();
        $wishlistCollection->getSelect()
            ->joinLeft(
                array('w_item' => $wishlistCollection->getResource()->getTable('wishlist/item')),
                'main_table.wishlist_id = w_item.wishlist_id',
                array('wishlist_item_id')
            )
            ->where('wishlist_item_id IS NOT NULL')
            ->order('RAND()')
            ->limit(1);
        $wishlist = Mage::getModel('wishlist/wishlist');
        if ($wishlistCollection->getSize() > 0) {
            $wishlist->load($wishlistCollection->getFirstItem()->getId());
        }

        $testEmail = Mage::getSingleton('email/config')->getTestEmail();

        $store = $defaultStoreId = Mage::app()->getWebsite(true)
            ->getDefaultGroup()
            ->getDefaultStore();

        $args = array(
            'customer_id' => $customer->getId(),
            'customer_email' => $testEmail,
            'customer_name' => $customer->getName(),
            'quote_id' => $quote->getId(),
            'order_id' => $order->getId(),
            'wishlist_id' => $wishlist->getId(),
            'time' => time(),
            'store_id' => $store->getId(),
        );

        return $args;
    }
}
