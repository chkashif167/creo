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



class Mirasvit_Advr_Block_Adminhtml_Catalog_Lowstock extends Mirasvit_Advr_Block_Adminhtml_Catalog_Abstract
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Low stock'));

        return $this;
    }

    protected function prepareChart()
    {
        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid()
            ->setDefaultSort('product_stock_qty')
            ->setDefaultDir('asc');

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('advr/report_sales')
            ->setBaseTable('catalog/product')
            ->setFilterData($this->getFilterData())
            ->selectColumns('product_id')
            ->selectColumns($this->getVisibleColumns())
            ->groupByColumn('product_id');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'product_sku'         => array(
                'header'              => 'SKU',
                'type'                => 'text',
                'totals_label'        => 'Total',
                'filter_totals_label' => 'Subtotal',
                'link_callback'       => array($this, 'rowUrlCallback'),
            ),

            'product_name'        => array(
                'header' => 'Product',
            ),

            'product_stock_qty'   => array(
                'header' => 'Stock Quantity',
                'type'   => 'number',
            ),

            'product_is_in_stock' => array(
                'header'  => 'Stock Availability',
                'type'    => 'options',
                'options' => Mage::getSingleton('advr/system_config_source_stock')->toOptionHash(),
            ),
        );

        $columns += $this->getBaseProductColumns(true);

        $columns['actions'] = array(
            'header'  => 'Actions',
            'actions' => array(
                array(
                    'caption'  => Mage::helper('advr')->__('View Product'),
                    'callback' => array($this, 'rowUrlCallback')
                ),
            ),
        );

        return $columns;
    }

    public function rowUrlCallback($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getData('product_id')));
    }
}
