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



class Mirasvit_Advr_Block_Adminhtml_Order_Customer extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Sales By Customer'));

        return $this;
    }

    protected function prepareChart()
    {
        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid()
            ->setDefaultSort('sum_grand_total')
            ->setDefaultDir('desc')
            ->setRowUrlCallback(array($this, 'rowUrlCallback'));

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('advr/report_sales')
            ->setBaseTable('sales/order')
            ->setFilterData($this->getFilterData())
            ->selectColumns(array_merge(
                array('customer_firstname', 'customer_lastname', 'customer_id'),
                $this->getVisibleColumns()
            ))
            ->addFieldToFilter('customer_group_id', array('gt' => 0))
            ->groupByColumn('customer_id');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'customer_email' => array(
                'header' => 'Customer',
                'totals_label' => 'Total',
                'filter_totals_label' => 'Subtotal',
                'frame_callback' => array(Mage::helper('advr/callback'), 'linkToCustomer'),
                'chart' => true,
            ),

            'customer_name' => array(
                'header' => 'Customer Name',
            ),

            'customer_group_id' => array(
                'header' => 'Customer Group',
                'chart' => false,
                'type' => 'options',
                'options' => Mage::getSingleton('advr/system_config_source_customerGroup')->toOptionHash(),
            ),

            'customer_company' => array(
                'header' => 'Customer Company',
                'filter' => false,
                'hidden' => true,
            ),

        );

        $columns += $this->getOrderTableColumns(true);

        return $columns;
    }

    public function rowUrlCallback($row)
    {
        if ($row->getCustomerId()) {
            return $this->getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId()));
        }

        return false;
    }
}
