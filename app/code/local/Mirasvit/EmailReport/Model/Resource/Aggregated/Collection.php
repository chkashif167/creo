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


class Mirasvit_EmailReport_Model_Resource_Aggregated_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_selectedColumns    = array();
    protected $_isTotals           = false;

    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('emailreport/aggregated');
        $this->setConnection($this->getResource()->getReadConnection());

        $this->_applyFilters = false;
    }

    protected function _getPeriodExpr()
    {
        if ('year' == $this->_period) {
            return $this->getDateFormatSql('period', '%Y');
        } elseif ('month' == $this->_period) {
            return $this->getDateFormatSql('period', '%Y-%m');
        } elseif ('day' == $this->_period) {
            return $this->getDateFormatSql('period', '%Y-%m-%d');
        }
    }

    public function getDateFormatSql($date, $format)
    {
        $expr = sprintf("DATE_FORMAT(%s, '%s')", $date, $format);
        return new Zend_Db_Expr($expr);
    }


    protected function _getSelectedColumns()
    {
        $adapter = $this->getConnection();

        if (!$this->_selectedColumns) {
            if ($this->isTotals()) {
                $this->_selectedColumns = $this->getAggregatedColumns();
            } else {
                $this->_selectedColumns = array(
                    'period'       =>  $this->_getPeriodExpr(),
                    'emails'        => 'SUM(emails)',
                    'opens'        => 'SUM(opens)',
                    'clicks'       => 'SUM(clicks)',
                    'reviews'      => 'SUM(reviews)',
                    'orders'       => 'SUM(orders)',
                    'revenue'      => 'SUM(revenue)',
                    'trigger_id'   => 'MAX(trigger_id)'
                );
            }
        }

        return $this->_selectedColumns;
    }

    protected function _makeBoundarySelect($from, $to)
    {
        $adapter = $this->getConnection();
        $cols    = $this->_getSelectedColumns();

        $select  = $adapter->select()
            ->from($this->getResource()->getMainTable(), $cols)
            ->where('period >= ?', $from)
            ->where('period <= ?', $to)
            ->group(array($this->_getPeriodExpr(), 'trigger_id'));

        return $select;
    }

    protected function _initSelect()
    {
        $select = $this->getSelect();

        // if grouping by trigger, not by period
        if (!$this->_period) {
            $cols = $this->_getSelectedColumns();

            $mainTable = $this->getMainTable();
            $select->from($mainTable, $cols)
                ->group('trigger_id');

            return $this;
        }

        $mainTable = $this->getMainTable();
        $select->from($this->getMainTable(), $this->_getSelectedColumns())
            ->where('trigger_id > 0');

        if (!$this->isTotals()) {
            $select->group(array($this->_getPeriodExpr(), 'trigger_id'));
        }

        return $this;
    }

    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::ORDER);
        return $this->getConnection()->select()->from($select, 'COUNT(*)');
    }

    protected function _beforeLoad()
    {
        parent::_beforeLoad();

        if ($this->_period) {
            $selectUnions = array();

            // apply date boundaries (before calling $this->_applyDateRangeFilter())
            $dtFormat   = Varien_Date::DATE_INTERNAL_FORMAT;
            $periodFrom = (!is_null($this->_from) ? new Zend_Date($this->_from, $dtFormat) : null);
            $periodTo   = (!is_null($this->_to) ? new Zend_Date($this->_to, $dtFormat) : null);
            if ('year' == $this->_period) {
                if ($periodFrom) {
                    // not the first day of the year
                    if ($periodFrom->toValue(Zend_Date::MONTH) != 1 || $periodFrom->toValue(Zend_Date::DAY) != 1) {
                        $dtFrom = $periodFrom->getDate();
                        // last day of the year
                        $dtTo = $periodFrom->getDate()->setMonth(12)->setDay(31);
                        if (!$periodTo || $dtTo->isEarlier($periodTo)) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->toString($dtFormat),
                                $dtTo->toString($dtFormat)
                            );

                            // first day of the next year
                            $this->_from = $periodFrom->getDate()
                                ->addYear(1)
                                ->setMonth(1)
                                ->setDay(1)
                                ->toString($dtFormat);
                        }
                    }
                }

                if ($periodTo) {
                    // not the last day of the year
                    if ($periodTo->toValue(Zend_Date::MONTH) != 12 || $periodTo->toValue(Zend_Date::DAY) != 31) {
                        $dtFrom = $periodTo->getDate()->setMonth(1)->setDay(1);  // first day of the year
                        $dtTo = $periodTo->getDate();
                        if (!$periodFrom || $dtFrom->isLater($periodFrom)) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->toString($dtFormat),
                                $dtTo->toString($dtFormat)
                            );

                            // last day of the previous year
                            $this->_to = $periodTo->getDate()
                                ->subYear(1)
                                ->setMonth(12)
                                ->setDay(31)
                                ->toString($dtFormat);
                        }
                    }
                }

                if ($periodFrom && $periodTo) {
                    // the same year
                    if ($periodFrom->toValue(Zend_Date::YEAR) == $periodTo->toValue(Zend_Date::YEAR)) {
                        $dtFrom = $periodFrom->getDate();
                        $dtTo = $periodTo->getDate();
                        $selectUnions[] = $this->_makeBoundarySelect(
                            $dtFrom->toString($dtFormat),
                            $dtTo->toString($dtFormat)
                        );

                        $this->getSelect()->where('1<>1');
                    }
                }

            } elseif ('month' == $this->_period) {
                if ($periodFrom) {
                    // not the first day of the month
                    if ($periodFrom->toValue(Zend_Date::DAY) != 1) {
                        $dtFrom = $periodFrom->getDate();
                        // last day of the month
                        $dtTo = $periodFrom->getDate()->addMonth(1)->setDay(1)->subDay(1);
                        if (!$periodTo || $dtTo->isEarlier($periodTo)) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->toString($dtFormat),
                                $dtTo->toString($dtFormat)
                            );

                            // first day of the next month
                            $this->_from = $periodFrom->getDate()->addMonth(1)->setDay(1)->toString($dtFormat);
                        }
                    }
                }

                if ($periodTo) {
                    // not the last day of the month
                    if ($periodTo->toValue(Zend_Date::DAY) != $periodTo->toValue(Zend_Date::MONTH_DAYS)) {
                        $dtFrom = $periodTo->getDate()->setDay(1);  // first day of the month
                        $dtTo = $periodTo->getDate();
                        if (!$periodFrom || $dtFrom->isLater($periodFrom)) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->toString($dtFormat),
                                $dtTo->toString($dtFormat)
                            );

                            // last day of the previous month
                            $this->_to = $periodTo->getDate()->setDay(1)->subDay(1)->toString($dtFormat);
                        }
                    }
                }

                if ($periodFrom && $periodTo) {
                    // the same month
                    if ($periodFrom->toValue(Zend_Date::YEAR) == $periodTo->toValue(Zend_Date::YEAR)
                        && $periodFrom->toValue(Zend_Date::MONTH) == $periodTo->toValue(Zend_Date::MONTH)
                    ) {
                        $dtFrom = $periodFrom->getDate();
                        $dtTo = $periodTo->getDate();
                        $selectUnions[] = $this->_makeBoundarySelect(
                            $dtFrom->toString($dtFormat),
                            $dtTo->toString($dtFormat)
                        );

                        $this->getSelect()->where('1<>1');
                    }
                }

            }

            $this->_applyDateRangeFilter();

            // add unions to select
            if ($selectUnions) {
                $unionParts = array();
                $cloneSelect = clone $this->getSelect();
                $helper = Mage::getResourceHelper('core');
                $unionParts[] = '(' . $cloneSelect . ')';
                foreach ($selectUnions as $union) {
                    $query = $helper->getQueryUsingAnalyticFunction($union);
                    $unionParts[] = '(' . $query . ')';
                }
                $this->getSelect()->reset()->union($unionParts, Zend_Db_Select::SQL_UNION_ALL);

            }

            if ($this->isTotals()) {
                // calculate total
                $cloneSelect = clone $this->getSelect();
                $this->getSelect()->reset()->from($cloneSelect, $this->getAggregatedColumns());
            } else {
                // add sorting
                $this->getSelect()->order(array('period ASC'));
            }
        }

        return $this;
    }

    public function addOrderStatusFilter()
    {
        return $this;
    }

    public function setAggregatedColumns(array $columns)
    {
        $this->_aggregatedColumns = $columns;
        return $this;
    }

    public function getAggregatedColumns()
    {
        return $this->_aggregatedColumns;
    }

    public function setDateRange($from = null, $to = null)
    {
        $this->_from = $from;
        $this->_to   = $to;
        return $this;
    }

    public function setPeriod($period)
    {
        $this->_period = $period;
        return $this;
    }

    public function addStoreFilter($storeIds)
    {
        $this->_storesIds = $storeIds;
        return $this;
    }

    public function isTotals($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isTotals;
        }
        $this->_isTotals = $flag;
        return $this;
    }

    protected function _applyDateRangeFilter()
    {
        // Remember that field PERIOD is a DATE(YYYY-MM-DD) in all databases including Oracle
        if ($this->_from !== null) {
            $this->getSelect()->where('period >= ?', $this->_from);
        }
        if ($this->_to !== null) {
            $this->getSelect()->where('period <= ?', $this->_to);
        }

        return $this;
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_initSelect();
        if ($this->_applyFilters) {
            $this->_applyDateRangeFilter();
            $this->_applyStoresFilter();
            $this->_applyCustomFilter();
        }
        return parent::load($printQuery, $logQuery);
    }
}
