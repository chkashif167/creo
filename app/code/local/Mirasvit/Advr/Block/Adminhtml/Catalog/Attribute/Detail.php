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



class Mirasvit_Advr_Block_Adminhtml_Catalog_Attribute_Detail extends Mirasvit_Advr_Block_Adminhtml_Catalog_Abstract
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(
            Mage::helper('advr')->__(
                'Sales Report for "%s is %s"',
                Mage::registry('current_attribute')->getFrontendLabel(),
                Mage::registry('current_attribute_option')
            )
        );

        return $this;
    }

    protected function prepareChart()
    {
        $this->setChartType('column');

        $this->initChart()
            ->setXAxisType('datetime')
            ->setXAxisField('period');

        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid()
            ->setDefaultSort('period')
            ->setDefaultDir('asc')
            ->setDefaultLimit(100000)
            ->setPagerVisibility(false);

        return $this;
    }

    protected function prepareToolbar()
    {
        $this->initToolbar()
            ->setRangesVisibility(true);

        return $this;
    }

    protected function _prepareCollection()
    {
        $attributeField = 'product_attribute_' . Mage::registry('current_attribute')->getAttributeCode();

        $collection = Mage::getModel('advr/report_sales')
            ->setBaseTable('catalog/product')
            ->setFilterData($this->getFilterData())
            ->selectColumns($attributeField)
            ->selectColumns($this->getVisibleColumns())
            ->groupByColumn('period')
            ->groupByColumn('product_attribute_' . Mage::registry('current_attribute')->getAttributeCode())
            ->addFieldToFilter($attributeField, Mage::registry('current_attribute_value'));

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'period' => array(
                'header'              => 'Period',
                'type'                => 'text',
                'frame_callback'      => array(Mage::helper('advr/callback'), 'period'),
                'totals_label'        => 'Total',
                'filter_totals_label' => 'Subtotal',
                'grouped'             => true,
                'filter'              => false,
            ),
        );

        $columns += $this->getBaseProductColumns(true);

        return $columns;
    }
}
