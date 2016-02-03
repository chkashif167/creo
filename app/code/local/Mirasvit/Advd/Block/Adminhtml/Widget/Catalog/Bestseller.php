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



class Mirasvit_Advd_Block_Adminhtml_Widget_Catalog_Bestseller extends Mirasvit_Advd_Block_Adminhtml_Widget_Abstract_Grid
{
    public function getGroup()
    {
        return 'Catalog';
    }

    public function getName()
    {
        return 'Bestsellers';
    }

    public function prepareOptions()
    {
        $this->form->addField(
            'interval',
            'select',
            array(
                'name'   => 'interval',
                'label'  => Mage::helper('advr')->__('Period'),
                'value'  => $this->getParam('interval', Mirasvit_Advr_Helper_Date::LAST_24H),
                'values' => Mage::helper('advr/date')->getIntervals(true, true),
            )
        );

        $this->form->addField(
            'limit',
            'text',
            array(
                'name'  => 'limit',
                'label' => Mage::helper('advr')->__('Number products'),
                'value' => $this->getParam('limit', 5)
            )
        );

        $this->form->addField(
            'columns',
            'multiselect',
            array(
                'name'   => 'columns',
                'label'  => Mage::helper('advr')->__('Columns'),
                'value'  => $this->getParam('columns', array()),
                'values' => array(
                    array(
                        'value' => 'product_sku',
                        'label' => 'SKU',
                    ),
                    array(
                        'value' => 'product_name',
                        'label' => 'Name',
                    ),
                    array(
                        'value' => 'sum_item_qty_ordered',
                        'label' => 'QTY',
                    ),
                    array(
                        'value' => 'sum_item_row_total',
                        'label' => 'Total',
                    ),
                    array(
                        'value' => 'avg_item_base_price',
                        'label' => 'Price',
                    )
                ),
            )
        );

        return $this;
    }

    protected function _prepareCollection($grid)
    {
        $interval = Mage::helper('advr/date')->getInterval($this->getParam('interval'), true);

        $filterData = new Varien_Object(array(
            'from'      => $interval->getFrom()->get(Varien_Date::DATETIME_INTERNAL_FORMAT),
            'to'        => $interval->getTo()->get(Varien_Date::DATETIME_INTERNAL_FORMAT),
            'store_ids' => $this->getParam('store_ids')
        ));

        $collection = Mage::getModel('advr/report_sales')
            ->setBaseTable('catalog/product')
            ->setFilterData($filterData)
            ->selectColumns(array(
                'product_id',
                'product_sku',
                'product_name',
                'sum_item_row_total',
                'sum_item_qty_ordered',
                'avg_item_base_price'
            ))->groupByColumn('product_id')
            ->setOrder('sum_item_qty_ordered');

        $grid->setCollection($collection);

        return $this;
    }

    protected function _prepareColumns($grid)
    {
        if (in_array('product_sku', $this->getParam('columns', array()))) {
            $grid->addColumn('product_sku', array(
                'header'           => Mage::helper('advr')->__('SKU'),
                'sortable'         => false,
                'index'            => 'product_sku',
                'column_css_class' => 'nobr',
            ));
        }

        if (in_array('product_name', $this->getParam('columns', array()))) {
            $grid->addColumn('product_name', array(
                'header'   => Mage::helper('advr')->__('Name'),
                'sortable' => false,
                'index'    => 'product_name',
            ));
        }

        if (in_array('sum_item_qty_ordered', $this->getParam('columns', array()))) {
            $grid->addColumn('sum_item_qty_ordered', array(
                'header'           => Mage::helper('advr')->__('QTY'),
                'align'            => 'right',
                'sortable'         => false,
                'type'             => 'number',
                'index'            => 'sum_item_qty_ordered',
                'column_css_class' => 'nobr',
            ));
        }

        $baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();

        if (in_array('sum_item_row_total', $this->getParam('columns', array()))) {
            $grid->addColumn('sum_item_row_total', array(
                'header'           => Mage::helper('advr')->__('Total'),
                'align'            => 'right',
                'sortable'         => false,
                'type'             => 'currency',
                'currency_code'    => $baseCurrencyCode,
                'index'            => 'sum_item_row_total',
                'column_css_class' => 'nobr',
            ));
        }

        if (in_array('avg_item_base_price', $this->getParam('columns', array()))) {
            $grid->addColumn('avg_item_base_price', array(
                'header'           => Mage::helper('advr')->__('Price'),
                'align'            => 'right',
                'sortable'         => false,
                'type'             => 'currency',
                'currency_code'    => $baseCurrencyCode,
                'index'            => 'avg_item_base_price',
                'column_css_class' => 'nobr',
            ));
        }

        $grid->setFilterVisibility(false);
        $grid->setPagerVisibility(false);
        $grid->setDefaultLimit($this->getParam('limit', 5));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getProductId()));
    }
}
