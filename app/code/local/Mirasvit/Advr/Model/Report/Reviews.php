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



class Mirasvit_Advr_Model_Report_Reviews extends Mirasvit_Advr_Model_Report_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order');

        $this->relations = array();

        $this->columns = array(
            'period'            => array(
                'expression_method' => 'getPeriodExpression',
                'table'             => 'review/review',
            ),

            'quantity'          => array(
                'expression' => 'COUNT(review_review_table.entity_id)',
                'table'      => 'review/review',
            ),

            'item_gross_profit' => array(
                'expression' => 'SUM(sales_order_item_table.base_row_total
                    - sales_order_item_table.qty_ordered * sales_order_item_table.base_cost)',
                'table'      => 'sales/order_item'
            ),
        );

    }

    public function getPeriodExpression()
    {
        return $this->_getRangeExpressionForAttribute(
            $this->getFilterData()->getRange(),
            $this->getTZDate('review_review_table.created_at')
        );
    }

    public function setFilterData($data)
    {
        $this->filterData = $data;

        $conditions = array();

        if ($this->filterData->getFrom()) {
            $conditions[] = $this->getTZDate('review_review_table.created_at')
                . " >= '"
                . $this->filterData->getFrom() . "'";
        }

        if ($this->filterData->getTo()) {
            $conditions[] = $this->getTZDate('review_review_table.created_at')
                . " < '"
                . $this->filterData->getTo() . "'";
        }

        $this->joinRelatedDependencies('review/review');

        foreach ($conditions as $condition) {
            $this->getSelect()->where($condition);
        }

        return $this;
    }
}
