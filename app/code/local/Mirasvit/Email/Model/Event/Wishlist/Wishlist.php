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



class Mirasvit_Email_Model_Event_Wishlist_Wishlist extends Mirasvit_Email_Model_Event_Abstract
{
    const EVENT_CODE = 'wishlist_wishlist|';

    public function getEventsGroup()
    {
        return Mage::helper('email')->__('Wishlist');
    }

    public function getEvents()
    {
        $result = array();

        $result[self::EVENT_CODE.'productadded'] = Mage::helper('email')->__('Product was added to wishlist');
        $result[self::EVENT_CODE.'shared'] = Mage::helper('email')->__('Wishlist shared');

        return $result;
    }

    public function findEvents($eventCode, $from)
    {
        $events = array();

        if ($eventCode == self::EVENT_CODE.'productadded') {
            $createdFrom = date('Y-m-d H:i:s', $from);
            $resource = Mage::getSingleton('core/resource');
            $read = $resource->getConnection('core_read');

            $attrFirstname = Mage::getSingleton('eav/config')->getAttribute('customer', 'firstname');
            $attrLastname = Mage::getSingleton('eav/config')->getAttribute('customer', 'lastname');
            $select = $read->select()
                ->from(array('wl' => $resource->getTableName('wishlist/wishlist')),
                    array(
                        'wishlist_id',
                        'customer_id',
                        'customer_name' => 'CONCAT(c_firstname.value , " ", c_lastname.value)',
                    )
                )
                ->join(array('wi' => $resource->getTableName('wishlist/item')),
                    'wi.wishlist_id = wl.wishlist_id',
                    array('added_at', 'store_id', 'product_id', 'wishlist_item_id')
                )
                ->join(array('c' => $resource->getTableName('customer/entity')),
                    'customer_id = c.entity_id',
                    array('email')
                )
                ->joinLeft(array('c_firstname' => $attrFirstname->getBackend()->getTable()),
                    'c.entity_id = c_firstname.entity_id AND c_firstname.attribute_id = '.$attrFirstname->getAttributeId(),
                    array()
                )
                ->joinLeft(array('c_lastname' => $attrLastname->getBackend()->getTable()),
                    'c.entity_id = c_lastname.entity_id AND c_lastname.attribute_id = '.$attrLastname->getAttributeId(),
                    array()
                )
                ->where('added_at > ?', $createdFrom);

            $wishs = $read->fetchAll($select);
            foreach ($wishs as $wish) {
                $events[] = array(
                    'expire_after' => 0,
                    'time' => strtotime($wish['added_at']),
                    'customer_email' => $wish['email'],
                    'customer_id' => $wish['customer_id'],
                    'customer_name' => $wish['customer_name'],
                    'store_id' => $wish['store_id'],
                    'product_id' => $wish['product_id'],
                    'wishlist_id' => $wish['wishlist_id'],
                    'wishlist_item_id' => $wish['wishlist_item_id'],
                );
            }
        }

        return $events;
    }

    public function observer($eventCode, $observer)
    {
        $key = array();
        if ($eventCode == self::EVENT_CODE.'shared') {
            $wishlist = $observer->getWishlist();
            $customer = Mage::getModel('customer/customer')->load($wishlist->getCustomerId());

            $key[] = $customer->getEmail();
            $key[] = $wishlist->getId();

            $args = array(
                'time' => time(),
                'customer_email' => $customer->getEmail(),
                'customer_name' => $customer->getName(),
                'customer_id' => $customer->getId(),
                'store_id' => $wishlist->getStore()->getId(),
                'wishlist_id' => $wishlist->getId(),
            );

            $this->saveEvent($eventCode, implode('|', $key), $args);
        }
    }
}
