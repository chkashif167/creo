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


class Mirasvit_FeedExport_Block_Adminhtml_Report_Product_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    protected $_columnGroupBy = 'period';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
    }

    public function getResourceCollectionName()
    {
        return 'feedexport/performance_product_collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'          => __('Period'),
            'index'           => 'period',
            'width'           => 100,
            'sortable'        => false,
            'period_type'     => $this->getPeriodType(),
            'renderer'        => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'    => __('Total'),
            'subtotals_label' => Mage::helper('feedexport')->__('SubTotal'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('feed_id', array(
            'header'          => __('Feed Id'),
            'index'           => 'feed_id',
            'width'           => 50,
            'sortable'        => false,
            'totals_label'    => '',
        ));

        $this->addColumn('product_sku', array(
            'header'          => __('Product Sku'),
            'index'           => 'product_sku',
            'width'           => 100,
            'sortable'        => false,
            'totals_label'    => '',
        ));

        $this->addColumn('product_name', array(
            'header'          => __('Product Name'),
            'index'           => 'product_name',
            'width'           => 100,
            'sortable'        => false,
            'totals_label'    => '',
        ));

        $this->addColumn('clicks', array(
            'header'    => __('Number of Clicks'),
            'index'     => 'clicks',
            'type'      => 'number',
            'sortable'  => false
        ));

        $this->addColumn('orders', array(
            'header'   => __('Number of Orders'),
            'index'    => 'orders',
            'type'     => 'number',
            'sortable' => false
        ));

        $currencyCode = $this->getCurrentCurrencyCode();
        $rate         = $this->getRate($currencyCode);

        $this->addColumn('product_price', array(
            'header'          => __('Product Price'),
            'index'           => 'product_price',
            'type'            => 'currency',
            'currency_code'   => $currencyCode,
            'rate'            => $rate,
            'sortable'        => false,
        ));

        $this->addColumn('revenue', array(
            'header'        => __('Revenue'),
            'index'         => 'revenue',
            'type'          => 'currency',
            'sortable'      => false,
            'currency_code' => $currencyCode,
            'rate'          => $rate,
        ));

        return parent::_prepareColumns();
    }

    protected function getFilterData()
    {
        $date = Mage::getSingleton('core/date');
        $data = parent::getFilterData();

        if (!$data->hasData('from')) {
            $data->setData('from', $date->gmtDate(null, $date->gmtTimestamp() - 30 * 24 * 60 * 60));
        }

        if (!$data->hasData('to')) {
            $data->setData('to', $date->gmtDate(null, $date->gmtTimestamp()));
        }

        if (!$data->hasData('period_type')) {
            $data->setData('period_type', 'day');
        }

        return $data;
    }
}