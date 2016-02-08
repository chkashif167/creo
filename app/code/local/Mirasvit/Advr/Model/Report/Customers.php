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



class Mirasvit_Advr_Model_Report_Customers extends Mirasvit_Advr_Model_Report_Abstract
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _construct()
    {
        $this->_init('sales/order');

        $this->relations = array(
            array(
                'customer/entity',
                'customer/customer_group',
                'customer_entity_table.group_id = customer_customer_group_table.customer_group_id',
            ),
            array(
                'customer/entity',
                'customer/address_entity',
                'customer_entity_table.entity_id = customer_address_entity_table.parent_id',
            ),
            array(
                'customer/address_entity',
                'sales/order_address',
                'sales_order_address_table.customer_address_id = customer_address_entity_table.entity_id',
            ),
            array(
                'sales/order',
                'customer/entity',
                'sales_order_table.customer_id = customer_entity_table.entity_id',
            ),
            array(
                'sales/order',
                'sales/order_item',
                'sales_order_table.entity_id = sales_order_item_table.order_id',
            ),
            array(
                'catalog/product',
                'cataloginventory/stock_item',
                'catalog_product_table.entity_id = cataloginventory_stock_item_table.product_id',
            ),
        );

        $this->addColumn(
            'customer_id',
            array(
                'label'      => false,
                'expression' => 'customer_entity_table.entity_id',
                'table'      => 'customer/entity'
            )
        )->addColumn(
            'email',
            array(
                'label'      => false,
                'expression' => 'customer_entity_table.email',
                'table'      => 'customer/entity'
            )
        )->addColumn(
            'customer_group_id',
            array(
                'label'      => false,
                'expression' => 'customer_entity_table.group_id',
                'table'      => 'customer/entity',
            )
        )->addColumn(
            'customer_created_at',
            array(
                'label'      => false,
                'expression' => 'customer_entity_table.created_at',
                'table'      => 'customer/entity',
            )
        )->addColumn(
            'last_order_at',
            array(
                'label'      => false,
                'expression' => 'MAX(sales_order_table.created_at)',
                'table'      => 'sales/order'
            )
        )->addColumn(
            'quantity',
            array(
                'label'      => false,
                'expression' => 'COUNT(sales_order_table.entity_id)',
                'table'      => 'sales/order'
            )
        )->addColumn(
            'sum_total_qty_ordered',
            array(
                'label'      => false,
                'expression' => 'SUM(sales_order_table.total_qty_ordered)',
                'table'      => 'sales/order'
            )
        )->addColumn(
            'sum_grand_total',
            array(
                'label'      => false,
                'expression' => 'SUM(sales_order_table.base_grand_total)',
                'table'      => 'sales/order'
            )
        )->addColumn(
            'avg_grand_total',
            array(
                'label'      => false,
                'expression' => 'AVG(sales_order_table.base_grand_total)',
                'table'      => 'sales/order'
            )
        )->addColumn(
            'products',
            array(
                'label'      => false,
                'expression' => 'CONCAT_WS("@",
                        GROUP_CONCAT(sales_order_item_table.product_id SEPARATOR "^"),
                        GROUP_CONCAT(sales_order_item_table.name SEPARATOR "^"),
                        GROUP_CONCAT(sales_order_item_table.sku SEPARATOR "^"),
                        GROUP_CONCAT(sales_order_item_table.qty_ordered SEPARATOR "^")
                    )',
                'table'      => 'sales/order_item'
            )
        );

        $customerAttributes = Mage::getSingleton('advr/system_config_source_customerAttribute')->toOptionHash();
        foreach ($customerAttributes as $attrCode => $attrLabel) {
            if (isset($this->columns['customer_' . $attrCode])) {
                continue;
            }

            $this->addColumn(
                'customer_' . $attrCode,
                array(
                    'label'        => false,
                    'expression'   => 'customer_' . $attrCode . '_table.value',
                    'table_method' => 'joinCustomerAttribute',
                    'table_args'   => array(
                        'attribute' => $attrCode,
                    )
                )
            );
        }

        $addressAttributes = Mage::getSingleton('advr/system_config_source_customerAddressAttribute')->toOptionHash();
        foreach ($addressAttributes as $attrCode => $attrLabel) {
            if (isset($this->columns['customer_address_' . $attrCode])) {
                continue;
            }

            $this->addColumn(
                'customer_address_' . $attrCode,
                array(
                    'label'        => false,
                    'expression'   => 'customer_address_' . $attrCode . '_table.value',
                    'table_method' => 'joinCustomerAddressAttribute',
                    'table_args'   => array(
                        'attribute' => $attrCode,
                    )
                )
            );
        }

        return $this;
    }

    public function joinCustomerAttribute($args)
    {
        $attrCode = $args['attribute'];
        $tableName = 'customer_' . $attrCode . '_table';

        if (isset($this->joinedTables[$tableName])) {
            return $this;
        }

        $attr = Mage::getSingleton('eav/config')->getAttribute('customer', $attrCode);

        $conditons = array(
            $tableName . '.entity_id = customer_entity_table.entity_id',
            $tableName . '.attribute_id = ' . $attr->getAttributeId(),
        );

        $this->getSelect()->joinLeft(
            array($tableName => $attr->getBackend()->getTable()),
            implode(' AND ', $conditons),
            array()
        );
        $this->joinedTables[$tableName] = true;

        return $this;
    }

    public function joinAddressTable()
    {
        $tableName = 'customer_address_entity_table';

        if (isset($this->joinedTables[$tableName])) {
            return $this;
        }

        $this->getSelect()->joinLeft(
            array($tableName => $this->getTable('customer/address_entity')),
            'customer_entity_table.entity_id = ' . $tableName . '.parent_id',
            array()
        );

        $this->joinedTables[$tableName] = true;

        return $this;
    }

    public function joinCustomerAddressAttribute($args)
    {
        $attrCode = $args['attribute'];
        $tableName = 'customer_address_' . $attrCode . '_table';

        if (isset($this->joinedTables[$tableName])) {
            return $this;
        }

        $attr = Mage::getSingleton('eav/config')->getAttribute('customer_address', $attrCode);

        $conditions = array(
            $tableName . '.entity_id = customer_address_entity_table.entity_id',
            $tableName . '.attribute_id = ' . $attr->getAttributeId(),
        );

        $this->getSelect()->joinLeft(
            array($tableName => $attr->getBackend()->getTable()),
            implode(' AND ', $conditions),
            array()
        );
        $this->joinedTables[$tableName] = true;

        return $this;
    }

    public function setFilterData($data, $filterByStatus = true)
    {
        parent::setFilterData($data);

        $this->filterData = $data;

        $conditions = array();

        if (count($this->filterData->getStoreIds())) {
            $conditions[] = 'customer_entity_table.store_id IN(' . implode(',', $this->filterData->getStoreIds()) . ')';
        }

        foreach ($conditions as $condition) {
            $this->getSelect()->where($condition);
        }

        return $this;
    }
}
