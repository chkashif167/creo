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
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_FeedExport_Model_Rule_Condition_Performance extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $attributes = array(
            'clicks_7' => Mage::helper('feedexport')->__('Last 7-days Clicks'),
            'orders_7' => Mage::helper('feedexport')->__('Last 7-days Orders'),
            'revenue_7' => Mage::helper('feedexport')->__('Last 7-days Revenue'),
            'cr_7' => Mage::helper('feedexport')->__('Last 7-days Conversion Rate (%)'),
            'gt_7' => Mage::helper('feedexport')->__('Last 7-days Total Sales'),

            'clicks_14' => Mage::helper('feedexport')->__('Last 14-days Clicks'),
            'orders_14' => Mage::helper('feedexport')->__('Last 14-days Orders'),
            'revenue_14' => Mage::helper('feedexport')->__('Last 14-days Revenue'),
            'cr_14' => Mage::helper('feedexport')->__('Last 14-days Conversion Rate (%)'),
            'gt_14' => Mage::helper('feedexport')->__('Last 14-days Total Sales'),

            'clicks_30' => Mage::helper('feedexport')->__('Last 30-days Clicks'),
            'orders_30' => Mage::helper('feedexport')->__('Last 30-days Orders'),
            'revenue_30' => Mage::helper('feedexport')->__('Last 30-days Revenue'),
            'cr_30' => Mage::helper('feedexport')->__('Last 30-days Conversion Rate (%)'),
            'gt_30' => Mage::helper('feedexport')->__('Last 30-days Total Sales'),

            'gt_100000' => Mage::helper('feedexport')->__('Lifetime Total Sales'),
        );

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    public function collectValidatedAttributes($productCollection)
    {
        $attribute = $this->getAttribute();

        $arr = explode('_', $attribute);
        $type = $arr[0];
        $period = $arr[1];

        $date = new Zend_Date();
        $date->sub($period * 24 * 60 * 60);

        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');

        switch ($type) {
            case 'clicks':
                $expr = new Zend_Db_Expr('SUM(clicks)');
            break;

            case 'orders':
                $expr = new Zend_Db_Expr('SUM(orders)');
            break;

            case 'revenue':
                $expr = new Zend_Db_Expr('SUM(revenue)');
            break;

            case 'cr':
                $expr = new Zend_Db_Expr('SUM(orders) / SUM(clicks) * 100');
            break;

            case 'gt':
                $select = new Zend_Db_Expr('sales_order_item_table.base_row_total');
                $this->joinItemTable($productCollection, $date->toString('YYYY-MM-dd'));
            break;
        }

        if ($type != 'gt') {
            $select = $connection->select();
            $select->from(array('ta' => $resource->getTableName('feedexport/performance_aggregated')), array($expr))
                ->where('ta.product_id = e.entity_id')
                ->where('ta.period >= ?', $date->toString('YYYY-MM-dd'));
        }

        $productCollection->getSelect()->columns(array($attribute => $select));

        return $this;
    }

    public function getInputType()
    {
        return 'string';
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();
        $value = $object->getData($attrCode);

        return $this->validateAttribute($value);
    }

    public function getJsFormObject()
    {
        return 'rule_conditions_fieldset';
    }

    /**
     * Join SUM of base_row_total for each product in given period.
     *
     * @param Varien_Data_Collection_Db $productCollection
     * @param string                    $period
     *
     * @return $this
     */
    private function joinItemTable(Varien_Data_Collection_Db $productCollection, $period)
    {
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $subSelect = $connection->select();

        $subSelect
            ->from(array('catalog_product_table' => $resource->getTableName('catalog/product')),
                array(
                    'base_row_total' => 'SUM(sales_order_item_table.base_row_total)',
                    'sales_order_item_table'.'.product_id',
                )
            )
            ->joinLeft(array('sales_order_item_table' => $resource->getTableName('sales/order_item')),
                'sales_order_item_table.product_id = catalog_product_table.entity_id AND sales_order_item_table.parent_item_id IS NULL',
                array()
            )
            ->where('sales_order_item_table.created_at >= ?', $period)
            ->group('catalog_product_table.entity_id');

        $productCollection->getSelect()
            ->joinLeft(array('sales_order_item_table' => new Zend_Db_Expr('('.$subSelect->__toString().')')),
                'sales_order_item_table.product_id = e.entity_id',
                array()
            );

        return $this;
    }
}
