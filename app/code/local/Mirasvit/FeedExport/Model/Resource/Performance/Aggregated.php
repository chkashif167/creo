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


class Mirasvit_FeedExport_Model_Resource_Performance_Aggregated extends Mage_Sales_Model_Resource_Report_Bestsellers
{
    protected function _construct()
    {
        $this->_init('feedexport/performance_aggregated', 'id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {

        return parent::_beforeSave($object);
    }

    public function aggregate($from = null, $to = null)
    {
        $mainTable = $this->getMainTable();
        $adapter   = $this->_getWriteAdapter();

        $from = $this->_dateToUtc($from);
        $to   = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);

        if ($from !== null || $to !== null) {
            $subSelect = $this->_getTableDateRangeSelect(
                $this->getTable('feedexport/performance_click'),
                'created_at', 'created_at', $from, $to
            );
        } else {
            $subSelect = null;
        }

        $this->_clearTableByDateRange($mainTable, $from, $to, $subSelect);

        $periodExpr = "DATE(CONVERT_TZ(source_table.created_at, '+00:00', '" . $this->_getStoreTimezoneUtcOffset() . "'))";
        //     $this->getStoreTZOffsetQuery(
        //         array('source_table' => $this->getTable('feedexport/performance_click')),
        //         'source_table.created_at', $from, $to
        //     )
        // );
        // echo $periodExpr;die();
        $columns = array(
            'period'     => $periodExpr,
            'product_id' => 'source_table.product_id',
            'feed_id'    => 'source_table.feed_id',
            'clicks'     => new Zend_Db_Expr('COUNT(source_table.id)'),
        );

        $select = $adapter->select();

        $select->from(array('source_table' => $this->getTable('feedexport/performance_click')), $columns)
            ->group(array(
                $periodExpr,
                'source_table.product_id',
                'source_table.feed_id'
            ))
            ->useStraightJoin();

        $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
        $adapter->query($insertQuery);

        $columns = array(
            'period'     => $periodExpr,
            'product_id' => 'source_table.product_id',
            'feed_id'    => 'source_table.feed_id',
            'orders'     => new Zend_Db_Expr('COUNT(source_table.id)'),
            'revenue'    => new Zend_Db_Expr('SUM(source_table.price)'),
        );

        $select = $adapter->select();

        $select->from(array('source_table' => $this->getTable('feedexport/performance_order')), $columns)
            ->group(array(
                $periodExpr,
                'source_table.product_id',
                'source_table.feed_id'
            ))
            ->useStraightJoin();

        $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
        $adapter->query($insertQuery);

        return $this;
    }
}