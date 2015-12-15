<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_Mysql4_Order_Grid_Collection extends MageWorx_OrdersGrid_Model_Mysql4_Order_Grid_Collection_Abstract
{
    protected $_setFields = array();

    public function __construct($resource=null) {
        parent::__construct();
        $helper = Mage::helper('mageworx_ordersgrid');
        if ($helper->isEnabled() && $this->getSelect()!==null) {

            // aitoc   
            if ((string)Mage::getConfig()->getModuleConfig('Aitoc_Aitpermissions')->active=='true') $this->aitocAitpermissionsLimitCollectionByStore();
            if ((string)Mage::getConfig()->getModuleConfig('AW_Deliverydate')->active=='true') $this->awDeliverydate();
            $groupBy = false;

            if (Mage::app()->getRequest()->getControllerName()!='customer') {
                // orders grid
                $listColumns = $helper->getGridColumns();

                foreach ($listColumns as $column) {

                    switch ($column) {
                        case 'product_names':
                        case 'product_skus':
                        case 'product_options':
                            $this->setOrderItemTbl();
                            $groupBy = true;
                            break;

                        case 'payment_method':
                            $this->setFieldPaymentMethod();
                            $groupBy = true;
                            break;

                        case 'qnty':
                            $this->setOrderItemTbl();
                        case 'shipped':
                            $this->setShipmentTbl();
                            $groupBy = true;
                            break;
                        case 'tracking_number':
                            $this->setShipmentTbl();
                            $this->setShipmentTrackTbl();
                            $groupBy = true;
                            break;
                        case 'billing_company':
                        case 'billing_street':
                        case 'billing_city':
                        case 'billing_region':
                        case 'billing_country':
                        case 'billing_postcode':
                        case 'billing_telephone':
                            $this->setOrderAddressTbl('billing');
                            $groupBy = true;
                            break;
                        case 'shipping_company':
                        case 'shipping_street':
                        case 'shipping_city':
                        case 'shipping_region':
                        case 'shipping_country':
                        case 'shipping_postcode':
                        case 'shipping_telephone':
                            $this->setOrderAddressTbl('shipping');
                            $groupBy = true;
                            break;
                        case 'order_comment':
                            $this->setHistoryTbl();
                            $groupBy = true;
                            break;
                        case 'shipping_amount':
                        case 'base_shipping_amount':
                        case 'subtotal':
                        case 'base_subtotal':
                            $this->setOrderTbl();
                            $groupBy = true;
                            break;
                    }
                }

                if ($groupBy) $this->getSelect()->group('main_table.entity_id');
                //die($this->getSelect()->__toString());

                // amasty
                if ((string)Mage::getConfig()->getModuleConfig('Amasty_Orderattach')->active=='true') $this->setAmastyOrderattachTbl();
                if ((string)Mage::getConfig()->getModuleConfig('Amasty_Orderattr')->active=='true') $this->setAmastyOrderattrTbl();


            } else {
                // customers grid
                if (Mage::app()->getRequest()->getActionName()!='orders') return $this;
                $listColumns = $helper->getCustomerGridColumns();


                // for enterprise add salesarchive orders
                if ($helper->isMagetoEnterprise() && version_compare(Mage::getVersion(), '1.9.0', '>=')) {
                    $cloneSelect = clone $this->getSelect();
                    $union = Mage::getResourceModel('enterprise_salesarchive/order_collection')
                        ->getOrderGridArchiveSelect($cloneSelect);
                    $unionParts = array($cloneSelect, $union);
                    $this->getSelect()->reset()->union($unionParts, Zend_Db_Select::SQL_UNION_ALL);
                    $this->setShellRequest();
                }

                foreach ($listColumns as $column) {

                    switch ($column) {
                        case 'product_names':
                        case 'product_skus':
                        case 'product_options':
                            $this->setOrderItemTbl();
                            $groupBy = true;
                            break;

                        case 'payment_method':
                            $this->setFieldPaymentMethod();
                            $groupBy = true;
                            break;

                        case 'qnty':
                            $this->setOrderItemTbl();
                        case 'shipped':
                            $this->setShipmentTbl();
                            $groupBy = true;
                            break;
                        case 'tracking_number':
                            $this->setShipmentTbl();
                            $this->setShipmentTrackTbl();
                            $groupBy = true;
                            break;
                        case 'order_comment':
                            $this->setHistoryTbl();
                            $groupBy = true;
                            break;
                    }
                }
                if ($groupBy) $this->getSelect()->group('main_table.entity_id');
                if ($groupBy) $this->setShellRequest();

                foreach ($listColumns as $column) {
                    switch ($column) {
                        case 'status': $this->addFieldToSelect('status'); break;
                        case 'product_names':
                            $this->addFieldToSelect('product_names');
                            if ($helper->isShowThumbnails()) $this->addFieldToSelect('product_ids');
                            break;
                        case 'product_skus': $this->addFieldToSelect('skus'); break;
                        case 'product_options': $this->addFieldToSelect('product_options'); break;
                        case 'total_refunded': $this->addFieldToSelect('total_refunded'); break;
                        case 'base_total_refunded': $this->addFieldToSelect('base_total_refunded'); break;
                        case 'customer_email': $this->addFieldToSelect('customer_email'); break;
                        case 'customer_group': $this->addFieldToSelect('customer_group_id'); break;
                        case 'tax_amount': $this->addFieldToSelect('tax_amount'); break;
                        case 'base_tax_amount': $this->addFieldToSelect('base_tax_amount'); break;
                        case 'discount_amount': $this->addFieldToSelect('discount_amount'); break;
                        case 'base_discount_amount': $this->addFieldToSelect('base_discount_amount'); break;
                        case 'shipping_method': $this->addFieldToSelect('shipping_method'); $this->addFieldToSelect('shipping_description'); break;
                        case 'payment_method': $this->addFieldToSelect('method'); break;
                        case 'internal_credit':
                            if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) $this->addFieldToSelect('customer_credit_amount');
                            break;
                        case 'base_internal_credit':
                            if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) $this->addFieldToSelect('base_customer_credit_amount');
                            break;
                        case 'order_group': $this->addFieldToSelect('order_group_id'); break;
                        case 'qnty':
                            $this->addFieldToSelect('total_qty_shipped');
                            $this->addFieldToSelect('total_qty_invoiced');
                            $this->addFieldToSelect('total_qty_ordered');
                            $this->addFieldToSelect('total_qty_refunded');
                            break;
                        case 'weight': $this->addFieldToSelect('weight'); break;
                        case 'shipped': $this->addFieldToSelect('shipped'); break;
                        case 'tracking_number': $this->addFieldToSelect('tracking_number'); break;
                        case 'coupon_code': $this->addFieldToSelect('coupon_code'); break;
                        case 'billing_company': $this->addFieldToSelect('billing_company'); break;
                        case 'billing_city': $this->addFieldToSelect('billing_city'); break;
                        case 'billing_postcode': $this->addFieldToSelect('billing_postcode'); break;
                        case 'shipping_company': $this->addFieldToSelect('shipping_company'); break;
                        case 'shipping_city': $this->addFieldToSelect('shipping_city'); break;
                        case 'shipping_postcode': $this->addFieldToSelect('shipping_postcode'); break;
                        case 'is_edited': $this->addFieldToSelect('is_edited'); break;
                        case 'order_comment': $this->addFieldToSelect('order_comment'); break;
                    }
                }
                $this->addFieldToSelect('base_currency_code');
            }
        }

    }

    public function setOrderTbl()
    {
        if ($this->getSelect()!==null && !isset($this->_setFields['setOrderTbl'])) {
            $subselect = clone $this->getSelect();
            $subselect->reset();

            $subselect->from($this->getTable('sales/order'), array('entity_id', 'subtotal', 'base_subtotal', 'shipping_amount', 'base_shipping_amount'));

            $this->getSelect()->joinLeft(array('order_tbl'=>$subselect),
                'order_tbl.entity_id = main_table.entity_id',
                array(
                    'subtotal' => 'order_tbl.subtotal',
                    'base_subtotal' => 'order_tbl.base_subtotal',
                    'shipping_amount' => 'order_tbl.shipping_amount',
                    'base_shipping_amount' => 'order_tbl.base_shipping_amount',
                ));

            $this->_setFields['setOrderTbl'] = true;
        }
        return $this;
    }

    public function setOrderItemTbl() {
        if ($this->getSelect()!==null && !isset($this->_setFields['setOrderItemTbl'])) {
            //$this->getSelect()->columns(array('product_names' =>"(SELECT GROUP_CONCAT(name SEPARATOR '\n') FROM ".$this->getTable('sales/order_item')." WHERE parent_item_id IS NULL AND order_id=main_table.entity_id)"));
            $this->getSelect()->joinLeft(array('order_item_tbl'=>$this->getTable('sales/order_item')),
                'order_item_tbl.order_id = main_table.entity_id',
                array(
                    'product_names' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`name` SEPARATOR \'\n\')'),
                    'skus' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`sku` SEPARATOR \'\n\')'),
                    'product_ids' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`product_id` SEPARATOR \'\n\')'),
                    'product_options' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`product_options` SEPARATOR \'^\')'),
                    //'total_qty_refunded' => new Zend_Db_Expr('SUM(order_item_tbl.`qty_refunded`)'),
                    'total_qty_refunded' => new Zend_Db_Expr('
                        (SELECT SUM( `qty_refunded` )
                        FROM `sales_flat_order_item`
                        WHERE `order_id` = `main_table`.`entity_id`
                        AND `parent_item_id` IS NULL)'),
                    //'total_qty_invoiced' => new Zend_Db_Expr('SUM(order_item_tbl.`qty_invoiced`)')
                    'total_qty_invoiced' => new Zend_Db_Expr('
                        (SELECT SUM( `qty_invoiced` )
                        FROM `sales_flat_order_item`
                        WHERE `order_id` = `main_table`.`entity_id`
                        AND `parent_item_id` IS NULL)')
                ))
                ->where('order_item_tbl.`parent_item_id` IS NULL');
            if (in_array('order_comment', Mage::helper('mageworx_ordersgrid')->getGridColumns())) $this->getSelect()->group('main_table.entity_id');

            $this->_setFields['setOrderItemTbl'] = true;
        }
        return $this;
    }

//    public function joinProductThumbnail() {        
//        $connection = $this->getConnection('core_read');
//        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();                
//
//        $attributeId = $connection->fetchOne("SELECT `attribute_id` FROM `".$tablePrefix."eav_attribute` WHERE `attribute_code` = 'thumbnail' AND `frontend_input` = 'media_image'");
//        if (!$attributeId) return $this;
//        $this->getSelect()->joinLeft(array('catalog_product_entity_tbl'=>$tablePrefix.'catalog_product_entity_varchar'),
//                'catalog_product_entity_tbl.entity_id = order_item_tbl.`product_id` AND catalog_product_entity_tbl.`attribute_id` = '.$attributeId. ' AND catalog_product_entity_tbl.`store_id`=0',
//                array('thumbnail' => new Zend_Db_Expr('GROUP_CONCAT(catalog_product_entity_tbl.`value` SEPARATOR \'\n\')')));
//        return $this;
//    }

    public function setOrderAddressTbl($addressType='billing') {
        if ($this->getSelect()!==null  && !isset($this->_setFields['setOrderAddressTbl'.$addressType])) {
            $this->getSelect()->joinLeft(array('order_address_'.$addressType.'_tbl'=>$this->getTable('sales/order_address')),
                'order_address_'.$addressType.'_tbl.parent_id = main_table.entity_id AND order_address_'.$addressType.'_tbl.`address_type` = "'.$addressType.'"',
                array($addressType.'_company' => 'company', $addressType.'_street' => 'street', $addressType.'_city' => 'city', $addressType.'_region' => 'region', $addressType.'_country_id' => 'country_id', $addressType.'_postcode' => 'postcode', $addressType.'_telephone' => 'telephone')
            );
            $this->_setFields['setOrderAddressTbl'.$addressType] = true;
        }
        return $this;
    }

    public function setFieldPaymentMethod() {
        if ($this->getSelect()!==null) {
            $this->getSelect()->joinLeft(array('order_payment_tbl'=>$this->getTable('sales/order_payment')),
                'order_payment_tbl.parent_id = main_table.entity_id', 'method'
            //array('method' => new Zend_Db_Expr('GROUP_CONCAT(`method` SEPARATOR \'\n\')'))
            );
        }
        return $this;
    }

    public function setHistoryTbl() {
        if ($this->getSelect()!==null) {
            if (isset($this->_setFields['setOrderItemTbl'])) $this->setShellRequest();
            $this->getSelect()->joinLeft(array('history_tbl'=>$this->getTable('sales/order_status_history')),
                "history_tbl.parent_id = main_table.entity_id AND (history_tbl.comment <> NULL OR  history_tbl.comment <> '')",
                array (
                    'order_comment' => new Zend_Db_Expr('GROUP_CONCAT(history_tbl.`comment` SEPARATOR \'\n\')')
                )
            );
        }
        return $this;
    }


    public function setShipmentTbl() {
        if ($this->getSelect()!==null && !isset($this->_setFields['setShipmentTbl'])) {
            $this->getSelect()->joinLeft(array('shipment_tbl'=>$this->getTable('sales/shipment_grid')),
                'shipment_tbl.order_id = main_table.entity_id',
                array (
                    'shipped' => new Zend_Db_Expr('(IF(IFNULL(shipment_tbl.`entity_id`, 0)>0, 1, 0))'),
                    'total_qty_shipped' => new Zend_Db_Expr('
                          (SELECT SUM(`total_qty`)
                          FROM `sales_flat_shipment_grid`
                          WHERE `order_id` = `main_table`.`entity_id`)
                        ')
                )
            );
            $this->_setFields['setShipmentTbl'] = true;
        }
        return $this;
    }

    public function setShipmentTrackTbl() {
        if ($this->getSelect()!==null && !isset($this->_setFields['setShipmentTrackTbl'])) {
            if (version_compare(Mage::helper('mageworx_ordersgrid')->getMagetoVersion(), '1.6.0', '>=')) {
                $this->getSelect()->joinLeft(array('shipment_track_tbl'=>$this->getTable('sales/shipment_track')),
                    'shipment_track_tbl.parent_id = shipment_tbl.entity_id',
                    array (
                        'tracking_number' => new Zend_Db_Expr('GROUP_CONCAT(shipment_track_tbl.`track_number` SEPARATOR \'\n\')')
                    )
                );
            } else {
                $this->getSelect()->joinLeft(array('shipment_track_tbl'=>$this->getTable('sales/shipment_track')),
                    'shipment_track_tbl.parent_id = shipment_tbl.entity_id',
                    array (
                        'tracking_number' => new Zend_Db_Expr('GROUP_CONCAT(shipment_track_tbl.`number` SEPARATOR \'\n\')')
                    )
                );
            }

            $this->_setFields['setShipmentTrackTbl'] = true;
        }
        return $this;
    }

    public function setAmastyOrderattachTbl() {
        $attachments = Mage::getModel('amorderattach/field')->getCollection();
        $attachments->addFieldToFilter('show_on_grid', 1);
        $attachments->load();

        if ($attachments->getSize()) {
            $fields = array();
            foreach ($attachments as $attachment) {
                $fields[] = $attachment->getFieldname();
            }
            $this->getSelect()->joinLeft(
                array('attachments' => Mage::getModel('amorderattach/order_field')->getResource()->getTable('amorderattach/order_field')), "main_table.entity_id = attachments.order_id", $fields
            );
        }
    }
    public function setAmastyOrderattrTbl() {
        $attributes = Mage::getModel('eav/entity_attribute')->getCollection();
        $attributes->addFieldToFilter('entity_type_id', Mage::getModel('eav/entity')->setType('order')->getTypeId());
        $attributes->addFieldToFilter('show_on_grid', 1);
        $attributes->load();
        if ($attributes->getSize()){
            $fields = array();
            foreach ($attributes as $attribute) {
                $fields[] = $attribute->getAttributeCode();
            }

            $alias = 'main_table';
            $this->getSelect()
                ->joinLeft(
                    array('custom_attributes' => Mage::getModel('amorderattr/attribute')->getResource()->getTable('amorderattr/order_attribute')),
                    "$alias.entity_id = custom_attributes.order_id",
                    $fields
                );
        }
    }

    protected function aitocAitpermissionsLimitCollectionByStore() {
        if (!$this->getFlag('permissions_processed')) {
            if (Mage::getSingleton('aitpermissions/role')->isPermissionsEnabled()) {
                $AllowedStoreviews = Mage::getSingleton('aitpermissions/role')->getAllowedStoreviewIds();
                if (version_compare(Mage::getVersion(), '1.4.1.0', '>')) {
                    $this->addAttributeToFilter('main_table.store_id', array('in' => $AllowedStoreviews));
                } else {
                    $this->addAttributeToFilter('store_id', array('in' => $AllowedStoreviews));
                }
            }
            $this->setFlag('permissions_processed', true);
        }
    }

    protected function awDeliverydate() {
        $tableName = Mage::getModel('deliverydate/delivery')->getCollection()->getTable('deliverydate/delivery');
        $this->getSelect()->joinLeft(array('del' => $tableName), '`main_table`.`entity_id`=`del`.`order_id`', array('aw_deliverydate_date' => 'del.delivery_date'));
    }

    public function setShellRequest() {
        if ($this->getSelect()!==null) {
            $sql = $this->getSelect()->assemble();
            $this->getSelect()->reset()->from(array('main_table' => new Zend_Db_Expr('('.$sql.')')), '*');
            //echo $this->getSelect()->assemble(); exit;
        }
        return $this;
    }

//    public function load($a=null, $b=null) {
//        echo $this->getSelect(); exit;
//        parent::load($a, $b);
//    }

    // variant 1
    public function getSelectCountSql() {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);

        $countSelect->columns('COUNT(DISTINCT main_table.entity_id)');

        return $countSelect;
    }

    // variant 2
//    public function getSize() {
//        if (is_null($this->_totalRecords)) {
//            $sql = $this->getSelectCountSql();
//            $sql = 'SELECT COUNT(*) FROM ('. $sql .') AS t1';
//            $this->_totalRecords = $this->getConnection()->fetchOne($sql, $this->_bindParams);
//        }
//        return intval($this->_totalRecords);
//    }

    public function addFieldToFilter($field, $condition = null) {
        if (in_array($field, array('created_at', 'increment_id', 'status', 'store_id', 'shipping_name', 'weight'))) $field = 'main_table.' . $field;
        return parent::addFieldToFilter($field, $condition);
    }

}