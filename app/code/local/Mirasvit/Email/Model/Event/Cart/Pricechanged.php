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


class Mirasvit_Email_Model_Event_Cart_Pricechanged extends Mirasvit_Email_Model_Event_Abstract
{
    const EVENT_CODE = 'cart_pricechanged';

    public function getEventsGroup()
    {
        return __('Shopping Cart');
    }

    public function getEvents()
    {
        $result = array();

        $result[self::EVENT_CODE] = __("Product price was changed");

        return $result;
    }

    public function findEvents($eventCode, $timestamp)
    {
        $events   = array();
        $resource = Mage::getSingleton('core/resource');
        $read     = $resource->getConnection('core_read');

        $select = $read->select()
            ->from(array('q' => $resource->getTableName('sales/quote')),
                array(
                    'store_id'    => 'q.store_id',
                    'quote_id'    => 'q.entity_id',
                    'customer_id' => 'q.customer_id',
                    'updated_at'  => 'q.updated_at'
                )
            )
            ->joinLeft(array('qa' => $resource->getTableName('sales/quote_address')),
                'q.entity_id = qa.quote_id AND qa.address_type = "billing"',
                array(
                    'customer_email'      => new Zend_Db_Expr('IFNULL(q.customer_email, qa.email)'),
                    'customer_firstname'  => new Zend_Db_Expr('IFNULL(q.customer_firstname, qa.firstname)'),
                    'customer_middlename' => new Zend_Db_Expr('IFNULL(q.customer_middlename, qa.middlename)'),
                    'customer_lastname'   => new Zend_Db_Expr('IFNULL(q.customer_lastname, qa.lastname)'),
                )
            )
            ->joinInner(array('qi' => $resource->getTableName('sales/quote_item')),
                'q.entity_id = qi.quote_id',
                array(
                    'i_updated_at' => new Zend_Db_Expr('MAX(qi.updated_at)'),
                    'old_price'    => 'qi.price',
                )
            )
            ->joinInner(array('pp' => $resource->getTableName('catalog/product_index_price')),
                'qi.product_id = pp.entity_id',
                array('new_price' => 'pp.final_price')
            )
            ->where('q.is_active = 1')
            ->where('q.items_count > 0')
            ->where('q.customer_email IS NOT NULL OR qa.email IS NOT NULL')
            ->where('qi.parent_item_id IS NULL')
            ->where('pp.final_price <> qi.price')
            ->group('q.entity_id')
            ->order('q.updated_at');

        $carts = $read->fetchAll($select);
        foreach ($carts as $cart) {
            $event = array(
                'time'           => strtotime($cart['updated_at']),
                'customer_email' => $cart['customer_email'],
                'customer_name'  => $cart['customer_firstname'].' '.$cart['customer_lastname'],
                'customer_id'    => $cart['customer_id'],
                'store_id'       => $cart['store_id'],
                'quote_id'       => $cart['quote_id'],
                'new_price'      => $cart['new_price'],
                'old_price'      => $cart['old_price'],
            );

            $events[] = $event;
        }

        return $events;
    }
}