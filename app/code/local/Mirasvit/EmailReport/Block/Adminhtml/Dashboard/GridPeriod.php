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


class Mirasvit_EmailReport_Block_Adminhtml_Dashboard_GridPeriod extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    protected $_columnGroupBy = 'period';

    protected $_resourceCollectionName  = 'emailreport/aggregated_collection';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'          => Mage::helper('emailreport')->__('Period'),
            'index'           => 'period',
            'width'           => 100,
            'sortable'        => false,
            'period_type'     => $this->getPeriodType(),
            'renderer'        => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'    => Mage::helper('emailreport')->__('Total'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('opens', array(
            'header'    => Mage::helper('emailreport')->__('Readers'),
            'index'     => 'opens',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false
        ));

        $this->addColumn('clicks', array(
            'header'    => Mage::helper('emailreport')->__('Clicks'),
            'index'     => 'clicks',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false
        ));

        $this->addColumn('reviews', array(
            'header'   => Mage::helper('emailreport')->__('Reviews'),
            'index'    => 'reviews',
            'type'     => 'number',
            'total'    => 'sum',
            'sortable' => false
        ));

        $this->addColumn('orders', array(
            'header'   => Mage::helper('emailreport')->__('Orders'),
            'index'    => 'orders',
            'type'     => 'number',
            'total'    => 'sum',
            'sortable' => false
        ));

        $currencyCode = $this->getCurrentCurrencyCode();
        $rate         = $this->getRate($currencyCode);

        $this->addColumn('revenue', array(
            'header'        => Mage::helper('emailreport')->__('Revenue'),
            'index'         => 'revenue',
            'type'          => 'currency',
            'total'         => 'sum',
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
