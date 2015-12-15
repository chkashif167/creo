<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_Grid extends Mage_Core_Model_Abstract
{

    protected $_listColumns = array();
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Set transaction isolation level for SESSION as READ COMMITTED
     * in order to avoid deadlocks
     *
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection
     */
    protected function setTransactionIsolationLevel(Mage_Core_Model_Resource_Db_Collection_Abstract $collection)
    {
        try {
            $connection = $collection->getConnection();
            $connection->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Modify select of orders grid collection
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @param array $listColumns
     * @return void
     */
    public function modifyOrdersGridCollection(
        Mage_Sales_Model_Resource_Order_Grid_Collection $collection,
        array $listColumns
    )
    {
        if (empty($listColumns)) {
            return;
        } else {
            $this->_listColumns = $listColumns;
        }

        $this->setTransactionIsolationLevel($collection);

        $this->setOrderItemTbl($collection);
        $groupBy = true;

        foreach ($listColumns as $column) {

            switch ($column) {
                case 'product_names':
                case 'product_skus':
                case 'product_options':
                    $groupBy = true;
                    break;
                case 'payment_method':
                    $this->setFieldPaymentMethod($collection);
                    $groupBy = true;
                    break;
                case 'qnty':
                    break;
                case 'shipped':
                    $this->setShipmentTbl($collection);
                    $groupBy = true;
                    break;
                case 'tracking_number':
                    $this->setShipmentTbl($collection);
                    $this->setShipmentTrackTbl($collection);
                    $groupBy = true;
                    break;
                case 'billing_company':
                case 'billing_street':
                case 'billing_city':
                case 'billing_region':
                case 'billing_country':
                case 'billing_postcode':
                case 'billing_telephone':
                    $this->setOrderAddressTbl('billing', $collection);
                    $groupBy = true;
                    break;
                case 'shipping_company':
                case 'shipping_street':
                case 'shipping_city':
                case 'shipping_region':
                case 'shipping_country':
                case 'shipping_postcode':
                case 'shipping_telephone':
                    $this->setOrderAddressTbl('shipping', $collection);
                    $groupBy = true;
                    break;
                case 'order_comment':
                    $groupBy = true;
                    break;
                case 'shipping_amount':
                case 'base_shipping_amount':
                case 'subtotal':
                case 'base_subtotal':
                    $this->setOrderTbl($collection);
                    $groupBy = true;
                    break;
                case 'status':
                    break;
            }
        }

        if ($groupBy) {
            $collection->getSelect()->group('main_table.entity_id');
        }

        Varien_Profiler::start('mw_addCustomColumnsSelect_updateCollection');
        $this->updateCollection($collection);
        $this->hideArchivedOrders($collection);
        Varien_Profiler::stop('mw_addCustomColumnsSelect_updateCollection');

        return;
    }

    /** Add default filter to collection
     * Do not show archived orders
     *
     * @param $collection
     */
    protected function hideArchivedOrders($collection)
    {
        $setDefaultFilter = true;
        $where = $collection->getSelect()->getPart('where');

        if (!empty($where)) {
            foreach ($where as $part) {
                if (stripos($part, 'order_group_id') !== false) {
                    $setDefaultFilter = false;
                    break;
                }
            }
        }

        if ($setDefaultFilter) {
            /** @var Varien_Db_Select $select */
            $select = $collection->getSelect();
            $where = $select->getPart('where');
            $and = '';
            if (!empty($where)) {
                $and = 'AND ';
            }
            $where[] = $and . "(order_group_id = '0')";
            $select->setPart('where', $where);
        }
    }

    /**
     * Modify select of customer orders grid collection
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @param array $listColumns
     * @return void
     */
    public function modifyCustomerOrdersGridCollection(
        Mage_Sales_Model_Resource_Order_Grid_Collection $collection,
        array $listColumns
    )
    {
        if (empty($listColumns)) {
            return;
        } else {
            $this->_listColumns = $listColumns;
        }

        foreach ($listColumns as $column) {
            switch ($column) {
                case 'status':
                    $collection->addFieldToSelect('status');
                    break;
                case 'total_refunded':
                    $collection->addFieldToSelect('total_refunded');
                    break;
                case 'base_total_refunded':
                    $collection->addFieldToSelect('base_total_refunded');
                    break;
                case 'customer_email':
                    $collection->addFieldToSelect('customer_email');
                    break;
                case 'customer_group':
                    $collection->addFieldToSelect('customer_group_id');
                    break;
                case 'tax_amount':
                    $collection->addFieldToSelect('tax_amount');
                    break;
                case 'base_tax_amount':
                    $collection->addFieldToSelect('base_tax_amount');
                    break;
                case 'discount_amount':
                    $collection->addFieldToSelect('discount_amount');
                    break;
                case 'base_discount_amount':
                    $collection->addFieldToSelect('base_discount_amount');
                    break;
                case 'shipping_method':
                    $collection->addFieldToSelect('shipping_method');
                    $collection->addFieldToSelect('shipping_description');
                    break;
                case 'internal_credit':
                    if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
                        $collection->addFieldToSelect('customer_credit_amount');
                    }
                    break;
                case 'base_internal_credit':
                    if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
                        $collection->addFieldToSelect('base_customer_credit_amount');
                    }
                    break;
                case 'order_group':
                    break;
                case 'weight':
                    $collection->addFieldToSelect('weight');
                    break;
                case 'qnty':
                    break;
                case 'coupon_code':
                    $collection->addFieldToSelect('coupon_code');
                    break;
                case 'is_edited':
                    $collection->addFieldToSelect('is_edited');
                    break;
            }
        }
        $collection->addFieldToSelect('base_currency_code');
        $collection->addFieldToSelect('order_group_id');

        /** important: If you wish to remove next line, you need to call setTransactionIsolationLevel at this method */
        $this->modifyOrdersGridCollection($collection, $listColumns);

        return;
    }

    /**
     * @return $this
     */
    public function setOrderTbl(Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        if ($collection->getSelect()!==null && !isset($collection->_setFields['setOrderTbl'])) {
            $subselect = clone $collection->getSelect();
            $subselect->reset();

            $subselect->from($collection->getTable('sales/order'), array('entity_id', 'subtotal', 'base_subtotal', 'shipping_amount', 'base_shipping_amount'));

            $collection->getSelect()->joinLeft(array('order_tbl'=>$subselect),
                'order_tbl.entity_id = main_table.entity_id',
                array(
                    'subtotal' => 'order_tbl.subtotal',
                    'base_subtotal' => 'order_tbl.base_subtotal',
                    'shipping_amount' => 'order_tbl.shipping_amount',
                    'base_shipping_amount' => 'order_tbl.base_shipping_amount',
                ));

            $collection->_setFields['setOrderTbl'] = true;
        }
        return $collection;
    }

    /**
     * @return $this
     */
    public function setOrderItemTbl(Mage_Sales_Model_Resource_Order_Grid_Collection $collection) {

        Varien_Profiler::start('setOrderItemTbl');
        if ($collection->getSelect()!==null && !isset($collection->_setFields['setOrderItemTbl'])) {

            $expressions = array (
                'product_names' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`name` SEPARATOR \'\n\')'),
                'skus' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`sku` SEPARATOR \'\n\')'),
                'product_ids' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`product_id` SEPARATOR \'\n\')'),
                'product_options' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`product_options` SEPARATOR \'^\')')
            );
            if (in_array('qnty', $this->_listColumns)) {
                $expressions2 = array(
                    'total_qty_refunded' => new Zend_Db_Expr('
                        (SELECT SUM( `qty_refunded` )
                        FROM ' . $collection->getTable('sales/order_item') . '
                        WHERE `order_id` = `main_table`.`entity_id`
                        AND `parent_item_id` IS NULL)'),
                    'total_qty_ordered_agregated' => new Zend_Db_Expr('
                        (SELECT SUM( `qty_ordered` )
                        FROM ' . $collection->getTable('sales/order_item') . '
                        WHERE `order_id` = `main_table`.`entity_id`
                        AND `parent_item_id` IS NULL)'),
                    'total_qty_canceled' => new Zend_Db_Expr('
                        (SELECT SUM( `qty_canceled` )
                        FROM ' . $collection->getTable('sales/order_item') . '
                        WHERE `order_id` = `main_table`.`entity_id`
                        AND `parent_item_id` IS NULL)'),
                    'total_qty_invoiced' => new Zend_Db_Expr('
                        (SELECT SUM( `qty_invoiced` )
                        FROM ' . $collection->getTable('sales/order_item') . '
                        WHERE `order_id` = `main_table`.`entity_id`
                        AND `parent_item_id` IS NULL)')
                );
                $expressions += $expressions2;
            }

            if (in_array('order_comment', $this->_listColumns)) {
                $expressions3 = array(
                    'order_comment' => new Zend_Db_Expr('
                        (SELECT GROUP_CONCAT(`comment` SEPARATOR \'\n\')
                        FROM ' . $collection->getTable('sales/order_status_history') . '
                        WHERE `parent_id` = `main_table`.`entity_id`)')
                );
                $expressions += $expressions3;
            }

            $collection->getSelect()->joinLeft(array('order_item_tbl'=>$collection->getTable('sales/order_item')),
                '`order_item_tbl`.`order_id` = `main_table`.`entity_id` AND `order_item_tbl`.`parent_item_id` IS NULL',
                $expressions
            );
            if (in_array('order_comment', $this->_listColumns)) {
                $collection->getSelect()->group('main_table.entity_id');
            }

            $collection->_setFields['setOrderItemTbl'] = true;
        }
        Varien_Profiler::stop('setOrderItemTbl');
        return $collection;
    }

    /**
     * @param string $addressType
     * @return $this
     */
    public function setOrderAddressTbl($addressType='billing', Mage_Sales_Model_Resource_Order_Grid_Collection $collection) {
        if ($collection->getSelect()!==null  && !isset($collection->_setFields['setOrderAddressTbl'.$addressType])) {
            $collection->getSelect()->joinLeft(array('order_address_'.$addressType.'_tbl'=>$collection->getTable('sales/order_address')),
                'order_address_'.$addressType.'_tbl.parent_id = main_table.entity_id AND order_address_'.$addressType.'_tbl.`address_type` = "'.$addressType.'"',
                array(
                    $addressType.'_company' => 'company',
                    $addressType.'_street' => 'street',
                    $addressType.'_city' => 'city',
                    $addressType.'_region' => 'region',
                    $addressType.'_country_id' => 'country_id',
                    $addressType.'_postcode' => 'postcode',
                    $addressType.'_telephone' => 'telephone'
                )
            );
            $collection->_setFields['setOrderAddressTbl'.$addressType] = true;
        }
        return $collection;
    }

    /**
     * @return $this
     */
    public function setFieldPaymentMethod(Mage_Sales_Model_Resource_Order_Grid_Collection $collection) {
        if ($collection->getSelect()!==null) {
            $collection->getSelect()->joinLeft(array('order_payment_tbl'=>$collection->getTable('sales/order_payment')),
                'order_payment_tbl.parent_id = main_table.entity_id', 'method'
            );
        }
        return $collection;
    }

    /**
     * @return $this
     */
    public function setShipmentTbl(Mage_Sales_Model_Resource_Order_Grid_Collection $collection) {
        if ($collection->getSelect()!==null && !isset($collection->_setFields['setShipmentTbl'])) {
            $collection->getSelect()->joinLeft(array('shipment_tbl'=>$collection->getTable('sales/shipment_grid')),
                'shipment_tbl.order_id = main_table.entity_id',
                array (
                    'shipped' => new Zend_Db_Expr('(IF(IFNULL(shipment_tbl.`entity_id`, 0)>0, 1, 0))'),
                    'total_qty_shipped' => new Zend_Db_Expr('
                          (SELECT SUM(`total_qty`)
                          FROM '.$collection->getTable('sales/shipment_grid').'
                          WHERE `order_id` = `main_table`.`entity_id`)
                        ')
                )
            );
            $collection->_setFields['setShipmentTbl'] = true;
        }
        return $collection;
    }

    /**
     * @return $this
     */
    public function setShipmentTrackTbl(Mage_Sales_Model_Resource_Order_Grid_Collection $collection) {
        if ($collection->getSelect()!==null && !isset($collection->_setFields['setShipmentTrackTbl'])) {
            if (version_compare(Mage::helper('mageworx_ordersgrid')->getMagentoVersion(), '1.6.0', '>=')) {
                $collection->getSelect()->joinLeft(array('shipment_track_tbl'=>$collection->getTable('sales/shipment_track')),
                    'shipment_track_tbl.parent_id = shipment_tbl.entity_id',
                    array (
                        'tracking_number' => new Zend_Db_Expr('GROUP_CONCAT(shipment_track_tbl.`track_number` SEPARATOR \'\n\')')
                    )
                );
            } else {
                $collection->getSelect()->joinLeft(array('shipment_track_tbl'=>$collection->getTable('sales/shipment_track')),
                    'shipment_track_tbl.parent_id = shipment_tbl.entity_id',
                    array (
                        'tracking_number' => new Zend_Db_Expr('GROUP_CONCAT(shipment_track_tbl.`number` SEPARATOR \'\n\')')
                    )
                );
            }

            $collection->_setFields['setShipmentTrackTbl'] = true;
        }
        return $collection;
    }

    /**
     * @return $this
     */
    public function setShellRequest(Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        if ($collection->getSelect()!==null) {
            $sql = $collection->getSelect()->assemble();
            $collection->getSelect()->reset()->from(array('main_table' => new Zend_Db_Expr('('.$sql.')')), '*');
        }
        return $collection;
    }

    /**
     * Join select as sub select (fix for total records bug)
     * Reset where from sub select to select
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @throws Zend_Db_Select_Exception
     */
    protected function updateCollection(Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        $select = $collection->getSelect();
        $oldSelect = clone $collection->getSelect();
        $oldWherePart = $oldSelect->getPart('where');
        $oldSelect->reset('where');
//        $select->setPart('where', str_ireplace('AND', '', array_pop($oldWherePart)));
//        $select->setPart('where', $oldWherePart);
        $select->reset();
        $select->from(array('main_table'=>$oldSelect));
        $select->setPart('where', $oldWherePart);
    }
}