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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Advr_Model_Report_Sales extends Mirasvit_Advr_Model_Report_Abstract
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _construct()
    {
        $this->_init('sales/order');

        $isNewCustomer = 'DATE_FORMAT(sales_order_table.created_at, "%Y-%m-%d")
            = DATE_FORMAT(customer_entity_table.created_at, "%Y-%m-%d")';

        $shippingTime = 'unix_timestamp(sales_shipment_table.created_at)
            - unix_timestamp(sales_order_table.created_at)';

        $this->relations = array(
            array(
                'sales/order',
                'sales/order_item',
                'sales_order_table.entity_id = sales_order_item_table.order_id',
            ),
            array(
                'sales/order',
                'customer/customer_group',
                'sales_order_table.customer_group_id = customer_customer_group_table.customer_group_id',
            ),
            array(
                'sales/order',
                'customer/entity',
                'sales_order_table.customer_id = customer_entity_table.entity_id',
            ),
            array(
                'sales/order',
                'sales/order_address',
                'sales_order_table.billing_address_id = sales_order_address_table.entity_id',
            ),
            array(
                'sales/order',
                'sales/order_payment',
                'sales_order_table.entity_id = sales_order_payment_table.parent_id',
            ),
            array(
                'advr/postcode',
                'sales/order_address',
                array(
                    'advr_postcode_table.postcode
                        = REPLACE(REPLACE(sales_order_address_table.postcode, " ", ""), "-","")',
                    'advr_postcode_table.country_id = sales_order_address_table.country_id',
                ),
            ),
            array(
                'sales/order_item',
                'catalog/product',
                array('sales_order_item_table.product_id = catalog_product_table.entity_id'),
                array($this, 'onJoinOrderItem'),
            ),
            array(
                'sales/order_item',
                'catalog/category_product',
                'sales_order_item_table.product_id = catalog_category_product_table.product_id',
            ),
            array(
                'catalog/category',
                'catalog/category_product',
                'catalog_category_product_table.category_id = catalog_category_table.entity_id',
            ),
            array(
                'sales/order',
                'sales/shipment',
                'sales_order_table.entity_id = sales_shipment_table.order_id',
            ),
            array(
                'catalog/product',
                'cataloginventory/stock_item',
                'catalog_product_table.entity_id = cataloginventory_stock_item_table.product_id',
            ),
            array(
                'sales/order',
                'sales/order_status',
                'sales_order_table.status = sales_order_status_table.status',
            ),
            array(
                'sales/order',
                'sales/invoice',
                'sales_order_table.entity_id = sales_invoice_table.entity_id',
            ),
        );

        $this->addColumn(
            'order_status',
            array(
                'label' => 'Status',
                'type' => 'options',
                'options' => Mage::getSingleton('advr/system_config_source_status')->toOptionHash(),
                'expression' => 'sales_order_table.status',
                'table' => 'sales/order',
            )
        )->addColumn(
            'order_status_label',
            array(
                'label' => 'Status Label',
                'type' => 'options',
                'options' => Mage::getResourceModel('sales/order_status_collection')->toOptionHash(),
                'expression' => 'sales_order_status_table.label',
                'table' => 'sales/order_status',
            )
        )->addColumn(
            'quantity',
            array(
                'label' => 'Number of orders',
                'type' => 'number',
                'expression' => 'COUNT(DISTINCT(sales_order_table.entity_id))',
                'table' => 'sales/order',
            )
        )->addColumn(
            'quantity_refunded',
            array(
                'label' => 'Number of refunded orders',
                'type' => 'number',
                'expression' => 'SUM(IF(sales_order_table.base_total_refunded > 0, 1, 0))',
                'table' => 'sales/order',
            )
        )->addColumn(
            'sum_total_qty_ordered',
            array(
                'label' => 'Total Qty Ordered',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_table.total_qty_ordered)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'sum_discount_amount',
            array(
                'label' => 'Discount Amount',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_table.base_discount_amount)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'sum_shipping_amount',
            array(
                'label' => 'Shipping Amount',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_table.base_shipping_amount)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'sum_tax_amount',
            array(
                'label' => 'Tax Amount',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_table.base_tax_amount)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'sum_total_refunded',
            array(
                'label' => 'Total Refunded',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_table.base_total_refunded)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'sum_shipping_tax_amount',
            array(
                'label' => 'Shipping Tax Amount',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_table.base_shipping_tax_amount)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'sum_total_invoiced',
            array(
                'label' => 'Total Invoiced',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_table.base_total_invoiced)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'sum_total_invoiced_cost',
            array(
                'label' => 'Total Invoiced Cost',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_table.base_total_invoiced_cost)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'sum_subtotal',
            array(
                'label' => 'Subtotal',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_table.base_subtotal)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'sum_grand_total',
            array(
                'label' => 'Grand Total',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_table.base_grand_total)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'sum_gross_profit',
            array(
                'label' => 'Gross Profit',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_table.base_subtotal_invoiced - sales_order_table.base_total_invoiced_cost)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'avg_total_qty_ordered',
            array(
                'label' => 'Avg Total Qty Ordered',
                'type' => 'currency',
                'expression' => 'AVG(sales_order_table.total_qty_ordered)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'avg_discount_amount',
            array(
                'label' => 'Avg Discount Amount',
                'type' => 'currency',
                'expression' => 'AVG(sales_order_table.base_discount_amount)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'avg_shipping_amount',
            array(
                'label' => 'Avg Shipping Amount',
                'type' => 'currency',
                'expression' => 'AVG(sales_order_table.base_shipping_amount)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'avg_tax_amount',
            array(
                'label' => 'Avg Tax Amount',
                'type' => 'currency',
                'expression' => 'AVG(sales_order_table.base_tax_amount)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'avg_total_refunded',
            array(
                'label' => 'Avg Total Refunded',
                'type' => 'currency',
                'expression' => 'AVG(sales_order_table.base_total_refunded)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'avg_shipping_tax_amount',
            array(
                'label' => 'Avg Shipping Tax Amount',
                'type' => 'currency',
                'expression' => 'AVG(sales_order_table.base_shipping_tax_amount)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'avg_total_invoiced',
            array(
                'label' => 'Avg Total Invoiced',
                'type' => 'currency',
                'expression' => 'AVG(sales_order_table.base_total_invoiced)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'avg_total_invoiced_cost',
            array(
                'label' => 'Avg Total Invoiced Cost',
                'type' => 'currency',
                'expression' => 'AVG(sales_order_table.base_total_invoiced_cost)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'avg_gross_profit',
            array(
                'label' => 'Avg Gross Profit',
                'type' => 'currency',
                'expression' => 'AVG(sales_order_table.base_subtotal_invoiced - order_table.base_total_invoiced_cost)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'avg_grand_total',
            array(
                'label' => 'Avg Grand Total',
                'type' => 'currency',
                'expression' => 'AVG(sales_order_table.base_grand_total)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'country_id',
            array(
                'label' => 'Country',
                'type' => 'options',
                'options' => Mage::getSingleton('advr/system_config_source_country')->toOptionHash(),
                'expression' => 'sales_order_address_table.country_id',
                'table' => 'sales/order_address',
            )
        )->addColumn(
            'state',
            array(
                'label' => 'State',
                'type' => 'text',
                'expression' => 'advr_postcode_table.state',
                'table' => 'advr/postcode',
            )
        )->addColumn(
            'province',
            array(
                'label' => 'Province',
                'type' => 'text',
                'expression' => 'advr_postcode_table.province',
                'table' => 'advr/postcode',
            )
        )->addColumn(
            'place',
            array(
                'label' => 'City / Place',
                'type' => 'text',
                'expression' => 'advr_postcode_table.place',
                'table' => 'advr/postcode',
            )
        )->addColumn(
            'lat',
            array(
                'label' => false,
                'expression' => 'advr_postcode_table.lat',
                'table' => 'advr/postcode',
            )
        )->addColumn(
            'lng',
            array(
                'label' => false,
                'expression' => 'advr_postcode_table.lng',
                'table' => 'advr/postcode',
            )
        )->addColumn(
            'postcode',
            array(
                'label' => 'Postcode',
                'type' => 'text',
                'expression' => 'advr_postcode_table.postcode',
                'table' => 'advr/postcode',
            )
        )->addColumn(
            'period',
            array(
                'label' => false,
                'expression_method' => 'getPeriodExpression',
                'table' => 'sales/order',
            )
        )->addColumn(
            'hour_of_day',
            array(
                'label' => 'Hour of Day',
                'type' => 'text',
                'expression_method' => 'getHourOfDayExpression',
                'table' => 'sales/order',
            )
        )->addColumn(
            'day_of_week',
            array(
                'label' => 'Day of Week',
                'type' => 'text',
                'expression_method' => 'getDayOfWeekExpression',
                'table' => 'sales/order',
            )
        )->addColumn(
            'payment_method',
            array(
                'label' => 'Payment Method',
                'expression' => 'sales_order_payment_table.method',
                'table' => 'sales/order_payment',
            )
        )->addColumn(
            'customer_group_id',
            array(
                'label' => 'Customer Group',
                'type' => 'options',
                'options' => Mage::getSingleton('advr/system_config_source_customerGroup')->toOptionHash(),
                'expression' => 'sales_order_table.customer_group_id',
                'table' => 'sales/order',
            )
        )->addColumn(
            'customer_group_code',
            array(
                'label' => false,
                'expression' => 'customer_customer_group_table.customer_group_code',
                'table' => 'customer/customer_group',
            )
        )->addColumn(
            'coupon_code',
            array(
                'label' => 'Coupon Code',
                'type' => 'text',
                'expression' => 'sales_order_table.coupon_code',
                'table' => 'sales/order',
            )
        )->addColumn(
            'is_new_customer',
            array(
                'label' => false,
                'expression' => $isNewCustomer,
                'table' => 'customer/entity',
            )
        )->addColumn(
            'quantity_by_new_customers',
            array(
                'label' => false,
                'expression' => 'SUM(IF('.$isNewCustomer.', 1, 0))',
                'table' => 'customer/entity',
            )
        )->addColumn(
            'sum_grand_total_by_new_customers',
            array(
                'label' => false,
                'expression' => 'SUM(IF('.$isNewCustomer.', base_grand_total, 0))',
                'table' => 'customer/entity',
            )
        )->addColumn(
            'quantity_by_returning_customers',
            array(
                'label' => false,
                'expression' => 'SUM(IF('.$isNewCustomer.', 0, 1))',
                'table' => 'customer/entity',
            )
        )->addColumn(
            'sum_grand_total_by_returning_customers',
            array(
                'label' => false,
                'expression' => 'SUM(IF('.$isNewCustomer.', 0, base_grand_total))',
                'table' => 'customer/entity',
            )
        )->addColumn(
            'customer_email',
            array(
                'label' => 'Email',
                'expression' => 'sales_order_table.customer_email',
                'table' => 'sales/order',
            )
        )->addColumn(
            'customer_id',
            array(
                'label' => false,
                'expression' => 'sales_order_table.customer_id',
                'table' => 'sales/order',
            )
        )->addColumn(
            'customer_name',
            array(
                'label' => 'Full Name',
                'expression' => 'CONCAT(
                    sales_order_table.customer_firstname,
                    " ",
                    sales_order_table.customer_lastname)',
                'table' => 'sales/order',
            )
        )->addColumn(
            'customer_firstname',
            array(
                'label' => 'First Name',
                'expression' => 'sales_order_table.customer_firstname',
                'table' => 'sales/order',
            )
        )->addColumn(
            'customer_lastname',
            array(
                'label' => 'Last Name',
                'expression' => 'sales_order_table.customer_lastname',
                'table' => 'sales/order',
            )
        )->addColumn(
            'customer_company',
            array(
                'label' => 'Customer Company',
                'expression' => 'sales_order_address_table.company',
                'table' => 'sales/order_address',
            )
        )->addColumn(
            'category_id',
            array(
                'label' => 'Category Id',
                'type' => 'number',
                'expression' => 'catalog_category_table.entity_id',
                'table' => 'catalog/category',
            )
        )->addColumn(
            'category_level',
            array(
                'label' => false,
                'expression' => 'catalog_category_table.level',
                'table' => 'catalog/category',
                'type' => 'number',
            )
        )->addColumn(
            'category_name',
            array(
                'label' => 'Category Name',
                'expression' => 'catalog_category_name_table.value',
                'table_method' => 'joinCategoryName',
            )
        )->addColumn(
            'category_path',
            array(
                'label' => false,
                'expression' => 'catalog_category_table.path',
                'table' => 'catalog/category',
            )
        )->addColumn(
            'sum_item_qty_ordered',
            array(
                'label' => 'Qty Ordered',
                'type' => 'number',
                'expression' => 'SUM(sales_order_item_table.qty_ordered)',
                'table' => 'sales/order_item',
            )
        )->addColumn(
            'sum_item_qty_refunded',
            array(
                'label' => 'Qty Refunded',
                'type' => 'number',
                'expression' => 'SUM(sales_order_item_table.qty_refunded)',
                'table' => 'sales/order_item',
            )
        )->addColumn(
            'sum_item_amount_refunded',
            array(
                'label' => 'Amount Refunded',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_item_table.base_amount_refunded)',
                'table' => 'sales/order_item',
            )
        )->addColumn(
            'sum_item_row_total',
            array(
                'label' => 'Row Total',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_item_table.base_row_total)',
                'table' => 'sales/order_item',
            )
        )->addColumn(
            'avg_item_base_price',
            array(
                'label' => 'Price',
                'type' => 'currency',
                'expression' => 'AVG(sales_order_item_table.base_price)',
                'table' => 'sales/order_item',
            )
        )->addColumn(
            'sum_item_tax_amount',
            array(
                'label' => 'Tax Amount',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_item_table.base_tax_amount)',
                'table' => 'sales/order_item',
            )
        )->addColumn(
            'sum_item_discount_amount',
            array(
                'label' => 'Discount Amount',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_item_table.base_discount_amount)',
                'table' => 'sales/order_item',
            )
        )->addColumn(
            'avg_shipping_time',
            array(
                'label' => false,
                'expression' => "AVG($shippingTime)",
                'table' => 'sales/shipment',
            )
        )->addColumn(
            'quantity_shipping_time_0_1',
            array(
                'label' => false,
                'expression' => "SUM(IF($shippingTime <= 3600, 1, 0))",
                'table' => 'sales/shipment',
            )
        )->addColumn(
            'quantity_shipping_time_1_24',
            array(
                'label' => false,
                'expression' => "SUM(IF($shippingTime > 3600 AND $shippingTime <= 86400, 1, 0))",
                'table' => 'sales/shipment',
            )
        )->addColumn(
            'quantity_shipping_time_24_48',
            array(
                'label' => false,
                'expression' => "SUM(IF($shippingTime > 86400 AND $shippingTime <= 172800, 1, 0))",
                'table' => 'sales/shipment',
            )
        )->addColumn(
            'quantity_shipping_time_48_72',
            array(
                'label' => false,
                'expression' => "SUM(IF($shippingTime > 172800 AND $shippingTime <= 259200, 1, 0))",
                'table' => 'sales/shipment',
            )
        )->addColumn(
            'quantity_shipping_time_72_',
            array(
                'label' => false,
                'expression' => "SUM(IF($shippingTime > 259200, 1, 0))",
                'table' => 'sales/shipment',
            )
        )->addColumn(
            'product_sku',
            array(
                'label' => 'SKU',
                'type' => 'text',
                'expression' => 'catalog_product_table.sku',
                'table' => 'catalog/product',
            )
        )->addColumn(
            'product_id',
            array(
                'label' => 'ID',
                'type' => 'number',
                'expression' => 'catalog_product_table.entity_id',
                'table' => 'catalog/product',
            )
        )->addColumn(
            'product_name',
            array(
                'label' => 'Name',
                'type' => 'text',
                'expression' => 'catalog_product_name_table.value',
                'table_method' => 'joinProductAttribute',
                'table_args' => array(
                    'attribute' => 'name',
                ),
            )
        )->addColumn(
            'product_attribute_set_id',
            array(
                'label' => 'Attribute Set',
                'type' => 'options',
                'expression' => 'catalog_product_table.attribute_set_id',
                'table' => 'catalog/product',
                'options' => $this->getAttributeSetOptions(),
            )
        )->addColumn(
            'product_stock_qty',
            array(
                'label' => 'Stock Qty',
                'type' => 'number',
                'expression' => 'cataloginventory_stock_item_table.qty',
                'table' => 'cataloginventory/stock_item',
            )
        )->addColumn(
            'product_is_in_stock',
            array(
                'label' => 'Stock Availability',
                'type' => 'number',
                'expression' => 'cataloginventory_stock_item_table.is_in_stock',
                'table' => 'cataloginventory/stock_item',
            )
        )->addColumn(
            'item_gross_profit',
            array(
                'label' => 'Gross Profit',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_item_table.base_row_total - sales_order_item_table.qty_ordered * sales_order_item_table.base_cost)',
                'table' => 'sales/order_item',
            )
        )->addColumn(
            'item_cost',
            array(
                'label' => 'Cost',
                'type' => 'currency',
                'expression' => 'SUM(sales_order_item_table.qty_ordered * sales_order_item_table.base_cost)',
                'table' => 'sales/order_item',
            )
        )->addColumn(
            'shipping_method',
            array(
                'label' => 'Shipping Method',
                'expression' => 'sales_order_table.shipping_method',
                'table' => 'sales/order',
            )
        );

        $attributes = Mage::getSingleton('advr/system_config_source_productAttribute')->toOptionHash();

        foreach ($attributes as $attrCode => $attrLabel) {
            if ($attrCode === 'sku') {
                continue;
            }

            $options = Mage::helper('advr')->getAttributeOptionHash($attrCode);

            $type = 'text';
            if ($options) {
                $type = 'options';
            }

            $this->addColumn(
                'product_attribute_'.$attrCode,
                array(
                    'label' => $attrLabel,
                    'type' => $type,
                    'options' => $options,
                    'expression' => 'catalog_product_'.$attrCode.'_table.value',
                    'table' => 'catalog/product',
                    'table_method' => 'joinProductAttribute',
                    'table_args' => array(
                        'attribute' => $attrCode,
                    ),
                )
            );
        }
    }

    public function getAttributeSetOptions()
    {
        $options = Mage::getModel('eav/entity_attribute_set')->getCollection()
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->toOptionHash();

        return $options;
    }

    public function getPeriodExpression()
    {
        return $this->_getRangeExpressionForAttribute(
            $this->getFilterData()->getRange(),
            $this->getTZDate('sales_order_table.created_at')
        );
    }

    public function getHourOfDayExpression()
    {
        return 'HOUR('.$this->getTZDate('sales_order_table.created_at').')';
    }

    public function getDayOfWeekExpression()
    {
        return new Zend_Db_Expr('WEEKDAY('.$this->getTZDate('sales_order_table.created_at').')');
    }

    public function joinCategoryName()
    {
        $tableName = 'catalog_category_name_table';

        if (isset($this->joinedTables[$tableName])) {
            return $this;
        }

        $this->joinRelatedDependencies('catalog/category');
        $category = Mage::getResourceSingleton('catalog/category');
        $attr = $category->getAttribute('name');
        $conditons = array(
            $tableName.'.entity_id = catalog_category_table.entity_id',
            $tableName.'.entity_type_id = '.$category->getTypeId(),
            $tableName.'.attribute_id = '.$attr->getAttributeId(),
            $tableName.'.store_id = 0',
        );

        $this->getSelect()->joinLeft(
            array($tableName => $attr->getBackend()->getTable()),
            implode(' AND ', $conditons),
            array()
        );
        $this->joinedTables[$tableName] = true;

        return $this;
    }

    public function joinProductAttribute($args)
    {
        $attrCode = $args['attribute'];
        $tableName = 'catalog_product_'.$attrCode.'_table';

        if (!isset($this->joinedTables[$tableName])) {
            $this->joinRelatedDependencies('sales/order_item');

            $product = Mage::getResourceSingleton('catalog/product');
            $attr = Mage::getSingleton('eav/config')->getAttribute($product->getTypeId(), $attrCode);

            $conditions = array();
            if ($this->getFilterData()->getIncludeChild()) {
                $conditions[] = $tableName.'.entity_id = sales_order_item_parent_table.product_id';
            } else {
                $conditions[] = $tableName.'.entity_id = sales_order_item_table.product_id';
            }

            $conditions[] = $tableName.'.attribute_id = '.$attr->getAttributeId();
            $conditions[] = $tableName.'.entity_type_id = '.$product->getTypeId();
            $conditions[] = $tableName.'.store_id = 0';

            $this->getSelect()->joinLeft(
                array($tableName => $attr->getBackend()->getTable()),
                implode(' AND ', $conditions),
                array()
            );
        }

        $this->joinedTables[$tableName] = true;

        return $this;
    }

    public function onJoinOrderItem($conditions)
    {
        if ($this->getFilterData()->getIncludeChild()) {
            $this->getSelect()
                ->joinLeft(
                    array('sales_order_item_parent_table' => $this->getTable('sales/order_item')),
                    'sales_order_item_parent_table.product_id = catalog_product_table.entity_id
                        AND (sales_order_item_parent_table.parent_item_id IS NOT NULL
                            OR sales_order_item_parent_table.product_type="simple")',
                    array()
                );

            $conditions = array();
            $conditions[] = 'sales_order_item_table.item_id =
                IFNULL(sales_order_item_parent_table.parent_item_id, sales_order_item_parent_table.item_id)';
        } else {
            $conditions[] = 'sales_order_item_table.parent_item_id IS NULL';
        }

        return $conditions;
    }

    public function setFilterData($data, $filterByStatus = true)
    {
        parent::setFilterData($data);

        $this->filterData = $data;

        $conditions = array();

        if ($this->filterData->getFrom()) {
            $conditions[] = $this->getTZDate('sales_order_table.created_at')
                ." >= '"
                .$this->filterData->getFrom()."'";
        }

        if ($this->filterData->getTo()) {
            $conditions[] = $this->getTZDate('sales_order_table.created_at')
                ." < '"
                .$this->filterData->getTo()."'";
        }

        if (count($this->filterData->getStoreIds())) {
            $conditions[] = 'sales_order_table.store_id IN('.implode(',', $this->filterData->getStoreIds()).')';
        }

        if ($filterByStatus) {
            $statuses = Mage::getSingleton('advr/config')->getProcessOrderStatuses();
            foreach ($statuses as $idx => $status) {
                $statuses[$idx] = "'$status'";
            }

            $conditions[] = '(sales_order_table.status IN('.implode(',', $statuses).')
                OR sales_order_table.status IS NULL)';
        }

        $this->joinRelatedDependencies('sales/order');

        foreach ($conditions as $condition) {
            $this->getSelect()->where($condition);
        }

        foreach (array_keys($data->getData()) as $column) {
            if (isset($this->columns[$column])) {
                $this->selectColumns($column);
                $cond = $this->columns[$column]->getFilter()->getCondition();
                $this->addFieldToFilter($column, $cond);
            }
        }

        return $this;
    }
}
