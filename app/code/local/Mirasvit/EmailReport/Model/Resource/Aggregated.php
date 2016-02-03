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


class Mirasvit_EmailReport_Model_Resource_Aggregated extends Mage_Sales_Model_Resource_Report_Bestsellers
{
    protected function _construct()
    {
        $this->_init('emailreport/aggregated', 'id');
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
                $this->getTable('emailreport/click'),
                'created_at', 'created_at', $from, $to
            );
        } else {
            $subSelect = null;
        }

        $this->_clearTableByDateRange($mainTable, $from, $to, $subSelect);

        $periodExpr = "DATE(CONVERT_TZ(source_table.created_at, '+00:00', '" . $this->_getStoreTimezoneUtcOffset() . "'))";

        $columns = array(
            'period'     => $periodExpr,
            'trigger_id' => 'source_table.trigger_id',
            'clicks'     => new Zend_Db_Expr('COUNT(source_table.id)'),
        );
        $this->_aggregate('emailreport/click', $columns);

        $columns = array(
            'period'     => $periodExpr,
            'trigger_id' => 'source_table.trigger_id',
            'orders'     => new Zend_Db_Expr('COUNT(source_table.id)'),
            'revenue'    => new Zend_Db_Expr('SUM(source_table.revenue)'),
        );
        $this->_aggregate('emailreport/order', $columns);

        $columns = array(
            'period'     => $periodExpr,
            'trigger_id' => 'source_table.trigger_id',
            'reviews'    => new Zend_Db_Expr('COUNT(source_table.id)'),
        );
        $this->_aggregate('emailreport/review', $columns);

        $columns = array(
            'period'     => $periodExpr,
            'trigger_id' => 'source_table.trigger_id',
            'opens'      => new Zend_Db_Expr('COUNT(source_table.id)')
        );
        $this->_aggregate('emailreport/open', $columns);


        $columns = array(
            'period'     => "DATE(CONVERT_TZ(source_table.scheduled_at, '+00:00', '" . $this->_getStoreTimezoneUtcOffset() . "'))",
            'trigger_id' => 'source_table.trigger_id',
            'emails'      => new Zend_Db_Expr('COUNT(source_table.trigger_id)')
        );
        $this->_aggregateQueue('email/queue', $columns, "`status` = 'delivered' and `scheduled_at` IS NOT NULL");

        return $this;
    }

    public function _aggregate($table, $columns, $where = null)
    {
        $mainTable  = $this->getMainTable();
        $adapter    = $this->_getWriteAdapter();
        $periodExpr = "DATE(CONVERT_TZ(source_table.created_at, '+00:00', '" . $this->_getStoreTimezoneUtcOffset() . "'))";
        $select     = $adapter->select();

        $select->from(array('source_table' => $this->getTable($table)), $columns)
            ->group(array(
                $periodExpr,
                'source_table.trigger_id'
            ))
            ->useStraightJoin();

        if ($where) {
            $select->where($where);
        }

        $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns));

        $adapter->query($insertQuery);


        return $this;
    }

    public function _aggregateQueue($table, $columns, $where = null)
    {
        $mainTable  = $this->getMainTable();
        $adapter    = $this->_getWriteAdapter();
        $periodExpr = "DATE(CONVERT_TZ(source_table.scheduled_at, '+00:00', '" . $this->_getStoreTimezoneUtcOffset() . "'))";
        $select     = $adapter->select();

        $select->from(array('source_table' => $this->getTable($table)), $columns)
            ->group(array(
                $periodExpr,
                'source_table.trigger_id'
            ))
            ->useStraightJoin();

        if ($where) {
            $select->where($where);
        }

        $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns));

        $adapter->query($insertQuery);


        return $this;
    }
}