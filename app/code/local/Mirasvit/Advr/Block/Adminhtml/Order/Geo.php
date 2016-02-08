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



class Mirasvit_Advr_Block_Adminhtml_Order_Geo extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Sales By Geo-data (based on Postal Code)'));

        return $this;
    }

    protected function prepareChart()
    {
        if ($this->getGeoDimension() == 'postcode') {
            $this->setChartType('map');
        } else {
            $this->setChartType('geo');
        }

        $this->initChart()
            ->resetColumns();

        if ($this->getFilterData()->getCountryId()) {
            $this->getChart()
                ->addOption('region', $this->getFilterData()->getCountryId())
                ->addOption('resolution', 'provinces')
                ->addOption('enableRegionInteractivity', true);
        }

        switch ($this->getGeoDimension()) {
            case 'state':
            case 'province':
                $this->getChart()
                    ->addOption('displayMode', 'regions')
                    ->addColumn('State', 'state')
                    ->addColumn('Grand Total', 'sum_grand_total', 'number')
                    ->addColumn('Number Of Orders', 'quantity', 'number');
                break;
            case 'place':
                $this->getChart()
                    ->addOption('displayMode', 'markers')
                    ->addColumn('Latitude', 'lat', 'number')
                    ->addColumn('Longitude', 'lng', 'number')
                    ->addColumn('Label', 'place', 'string')
                    ->addColumn('Grand Total', 'sum_grand_total', 'number')
                    ->addColumn('Number Of Orders', 'quantity', 'number');
                break;

            case 'postcode':
                $this->getChart()
                    ->addOption('mapTypeId', 'google.maps.MapTypeId.TERRAIN')
                    ->addOption('zoom', 3)
                    ->addOption('center', array('A' => 40.00, 'F' => 0.))
                    ->addColumn('Latitude', 'lat', 'number')
                    ->addColumn('Longitude', 'lng', 'number')
                    ->addColumn('Place', 'place', 'label')
                    ->addColumn('Postal Code', 'postcode', 'label')
                    ->addColumn('Grand Total', 'sum_grand_total', 'label')
                    ->addColumn('Number Of Orders', 'quantity', 'label');
                break;

            default:
                $this->getChart()
                    ->addColumn('Country', 'country_id')
                    ->addColumn('Grand Total', 'sum_grand_total', 'number');
                break;

        }

        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid();

        $this->getGrid()
            ->setDefaultSort('sum_grand_total')
            ->setDefaultDir('desc')
            ->setDefaultLimit(200)
            ->setPagerVisibility(true);

        return $this;
    }

    protected function prepareToolbar()
    {
        $this->initToolbar();

        $this->getToolbar()
            ->setRangesVisibility(false)
            ->setCompareVisibility(false);

        $form = $this->getToolbar()->getForm();

        $form->addField('geo_dimension', 'radios', array(
            'name'   => 'geo_dimension',
            'label'  => Mage::helper('advr')->__('Group By'),
            'values' => array(
                array(
                    'value' => 'country_id',
                    'label' => $this->__('Country')
                ),
                array(
                    'value' => 'state',
                    'label' => $this->__('State')
                ),
                array(
                    'value' => 'province',
                    'label' => $this->__('Province')
                ),
                array(
                    'value' => 'place',
                    'label' => $this->__('Place')
                ),
                array(
                    'value' => 'postcode',
                    'label' => $this->__('Postal Code')
                ),
            ),
            'value'  => $this->getGeoDimension(),
        ));

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('advr/report_sales')
            ->setBaseTable('sales/order')
            ->setFilterData($this->getFilterData())
            ->selectColumns(array('lat', 'lng'))
            ->selectColumns($this->getVisibleColumns());

        if ($this->getGeoDimension()) {
            $collection->groupByColumn($this->getGeoDimension());
        } else {
            $collection->groupByColumn('country_id');
        }

        return $collection;
    }

    public function getColumns()
    {
        $columns = array();

        $columns['country_id'] = array(
            'header'         => 'Country',
            'type'           => 'options',
            'frame_callback' => array(Mage::helper('advr/callback'), 'country'),
            'totals_label'   => 'Total',
            'options'        => Mage::getSingleton('advr/system_config_source_country')->toOptionHash(),
            'position'       => 1,
            'link_callback'  => array($this, 'countryLinkCallBack'),
        );

        if (in_array($this->getGeoDimension(), array('postcode', 'state', 'province', 'place'))) {
            $columns['state'] = array(
                'header'        => 'State',
                'totals_label'  => '',
                'hidden'        => false,
                'position'      => 2,
                'link_callback' => array($this, 'stateLinkCallBack'),
            );
        }

        if (in_array($this->getGeoDimension(), array('province'))) {
            $columns['province'] = array(
                'header'       => 'Province (District)',
                'totals_label' => '',
                'hidden'       => false,
                'position'     => 3,
            );
        }

        if (in_array($this->getGeoDimension(), array('postcode', 'place'))) {
            $columns['place'] = array(
                'header'        => 'Place (City)',
                'totals_label'  => '',
                'hidden'        => false,
                'position'      => 4,
                'link_callback' => array($this, 'placeLinkCallBack'),
            );
        }

        if (in_array($this->getGeoDimension(), array('postcode'))) {
            $columns['postcode'] = array(
                'header'       => 'Postal Code',
                'totals_label' => '',
                'hidden'       => false,
                'position'     => 5,
            );
        }

        $columns['percent'] = array(
            'header'          => 'Number Of Orders, %',
            'type'            => 'percent',
            'index'           => 'quantity',
            'frame_callback'  => array(Mage::helper('advr/callback'), 'percent'),
            'filter'          => false,
            'export_callback' => array(Mage::helper('advr/callback'), '_percent'),
        );

        $columns += $this->getOrderTableColumns();

        return $columns;
    }

    public function getGeoDimension()
    {
        if (!$this->getFilterData()->getGeoDimension()) {
            return 'country_id';
        }

        return $this->getFilterData()->getGeoDimension();
    }

    public function countryLinkCallBack($row)
    {
        $row->setGeoDimension('state');

        return Mage::helper('advr/callback')->rowUrl('*/*/*', $row, array('country_id', 'geo_dimension'));
    }

    public function stateLinkCallBack($row)
    {
        $row->setGeoDimension('place');

        return Mage::helper('advr/callback')->rowUrl('*/*/*', $row, array('country_id', 'state', 'geo_dimension'));
    }

    public function placeLinkCallBack($row)
    {
        $row->setGeoDimension('postcode');

        return Mage::helper('advr/callback')->rowUrl(
            '*/*/*',
            $row,
            array('country_id', 'state', 'place', 'geo_dimension')
        );
    }
}
