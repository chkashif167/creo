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


class Mirasvit_FeedExport_Model_Resource_Performance_Product_Collection
    extends Mage_Sales_Model_Mysql4_Report_Collection_Abstract
{
    protected $_periodFormat;
    protected $_selectedColumns = array();

    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('feedexport/performance_aggregated');
        $this->setConnection($this->getResource()->getReadConnection());
    }

    protected function _applyDateRangeFilter()
    {
        if (!is_null($this->_from)) {
            $this->getSelect()->where($this->_periodFormat . '>= ?', $this->_from);
        }
        if (!is_null($this->_to)) {
            $this->getSelect()->where($this->_periodFormat . '<= ?', $this->_to);
        }

        return $this;
    }

    public function _applyStoresFilter()
    {
        return $this;
    }

    protected function _getSelectedColumns()
    {
        if (!$this->_selectedColumns) {
            $this->_selectedColumns = array(
                'periods'       =>  sprintf('%s', $this->getDateFormatSql('period', '%Y-%m-%d')),
                'period'        =>  sprintf('%s', $this->getDateFormatSql('period', '%Y-%m-%d')),
                'product_id'    => 'product_id',
                'feed_id'       => 'feed_id',
                'clicks'        => 'SUM(clicks)',
                'orders'        => 'SUM(orders)',
                'revenue'       => 'SUM(revenue)',
            );
            if ('year' == $this->_period) {
                $this->_periodFormat = $this->getDateFormatSql('main_table.period', '%Y');
                $this->_from = date('Y', strtotime($this->_from));
                $this->_to = date('Y', strtotime($this->_to));
            } elseif ('month' == $this->_period) {
                $this->_periodFormat = $this->getDateFormatSql('main_table.period', '%Y-%m');
                $this->_from = date('Y-m', strtotime($this->_from));
                $this->_to = date('Y-m', strtotime($this->_to));
            } elseif ('day' == $this->_period) {
                $this->_periodFormat = $this->getDateFormatSql('main_table.period', '%Y-%m-%d');
            }
        }

        return $this->_selectedColumns;
    }

    protected function _initSelect()
    {
        $select = $this->getSelect();

        $select->from(
            array('main_table' => $this->getResource()->getMainTable()),
            $this->_getSelectedColumns()
        );

        $productAttributes = array(
            'product_name'      => 'name',
            'product_price'     => 'price',
        );
        $field = 'value';

        foreach ($productAttributes as $alias => $attributeCode) {
            $tableAlias = $attributeCode . '_table';
            $attribute  = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);

            if ($attributeCode === 'price') {
                $field = 'SUM('. $tableAlias . '.value)';
            }

            $this->getSelect()->joinLeft(
                array($tableAlias => $attribute->getBackendTable()),
                'main_table.product_id=' . $tableAlias . '.entity_id AND ' . $tableAlias . '.attribute_id=' . $attribute->getId(),
                array($alias => $field)
            );
        }

        $select->join(
            array('product' => $this->getTable('catalog/product')),
            'main_table.product_id = product.entity_id',
            array('product_sku' => 'sku')

        );

        if (!$this->isTotals() && !$this->isSubTotals()) {
            $select->group(array(
                $this->_periodFormat,
                'product_id'
            ));
        }
        if ($this->isSubTotals()) {
            $select->group(array(
                $this->_periodFormat,
            ));
        }

        return $this;
    }

    public function getDateFormatSql($date, $format)
    {
        $expr = sprintf("DATE_FORMAT(%s, '%s')", $date, $format);

        return new Zend_Db_Expr($expr);
    }
}