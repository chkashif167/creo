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



class Mirasvit_Advr_Block_Adminhtml_Order_Category extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Sales By Category'));

        return $this;
    }

    protected function prepareChart()
    {
        $this->setChartType('pie');

        $this->initChart()
            ->setNameField('category_name')
            ->setValueField('sum_grand_total');

        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid()
            ->setDefaultLimit(100000)
            ->setPagerVisibility(false);

        return $this;
    }

    public function _prepareCollection()
    {
        $collection = Mage::getModel('advr/report_sales')
            ->setBaseTable('catalog/category')
            ->setFilterData($this->getFilterData())
            ->selectColumns($this->getVisibleColumns())
            ->groupByColumn('category_id');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'category_level' => array(
                'header'   => 'Level',
                'type'     => 'number',
                'sortable' => false,
            ),

            'category_name'  => array(
                'header'         => 'Category',
                'frame_callback' => array(Mage::helper('advr/callback'), 'category'),
                'chart'          => true,
                'sortable'       => false,
            ),

            'category_path'  => array(
                'header'         => 'Category Path',
                'frame_callback' => array(Mage::helper('advr/callback'), 'categoryPath'),
                'sortable'       => false,
                'hidden'         => true
            )
        );

        $columns += $this->getOrderTableColumns();

        return $columns;
    }

    public function getTotals()
    {
        return false;
    }
}
