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



class Mirasvit_Advr_Block_Adminhtml_Block_Container extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Mirasvit_Advr_Block_Adminhtml_Block_Toolbar
     */
    protected $toolbar;

    /**
     * @var Mirasvit_Advr_Block_Adminhtml_Block_Grid
     */
    protected $grid;

    protected $chart;
    protected $storeSwitcher;

    public function _prepareLayout()
    {
        $this->prepareStoreSwitcher()
            ->prepareToolbar()
            ->prepareGrid()
            ->prepareChart();

        $this->setTemplate('mst_advr/block/container.phtml');

        return parent::_prepareLayout();
    }

    public function getGrid()
    {
        return $this->grid;
    }

    public function getToolbar()
    {
        return $this->toolbar;
    }

    public function getChart()
    {
        return $this->chart;
    }

    public function getStoreSwitcher()
    {
        return $this->storeSwitcher;
    }

    /**
     * @return $this
     */
    protected function prepareStoreSwitcher()
    {
        $this->initStoreSwitcher();

        return $this;
    }

    /**
     * @return $this
     */
    protected function prepareToolbar()
    {
        $this->initToolbar();

        return $this;
    }

    /**
     * @return $this
     */
    protected function prepareGrid()
    {
        $this->initGrid();

        return $this;
    }

    /**
     * @return $this
     */
    protected function prepareChart()
    {
        $this->initChart();

        return $this;
    }

    /**
     * @return $this
     */
    protected function initStoreSwitcher()
    {
        $this->storeSwitcher = Mage::app()->getLayout()->createBlock('adminhtml/store_switcher')
            ->setTemplate('mst_advr/block/store_switcher.phtml')
            ->setStoreVarName('store_ids');

        return $this;
    }

    /**
     * @return Mirasvit_Advr_Block_Adminhtml_Block_Toolbar
     */
    protected function initToolbar()
    {
        $this->toolbar = Mage::app()->getLayout()->createBlock('advr/adminhtml_block_toolbar');

        $this->toolbar
            ->setFilterData($this->getFilterData())
            ->setVisibility(true)
            ->setRangesVisibility(false)
            ->setCompareVisibility(false)
            ->setIntervalsVisibility(true)
            ->setContainer($this);

        return $this->toolbar;
    }

    /**
     * @return Mirasvit_Advr_Block_Adminhtml_Block_Grid
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function initGrid2()
    {

    }

    protected function initGrid()
    {
        $this->grid = Mage::app()->getLayout()->createBlock('advr/adminhtml_block_grid', get_class($this))
            ->setNameInLayout('grid');

        foreach ($this->getColumns() as $columnId => $column) {
            $this->grid->addColumn($columnId, $column);

            if (isset($column['grouped'])) {
                $this->grid->isColumnGrouped($columnId, 1);
            }
        }

        $this->grid->setContainer($this)
            ->setFilterData($this->getFilterData())
            ->afterCollectionLoad(array($this, 'afterGridCollectionLoad'));

        $this->grid->setCollection($this->getCollection());

        $totals = $this->getTotals();

        if ($totals) {
            $this->grid->setTotals($totals);
            $this->grid->setCountTotals(1);
        }

        $this->grid->addExportType('csv', Mage::helper('advr')->__('CSV'));
        $this->grid->addExportType('xml', Mage::helper('advr')->__('Excel XML'));

        return $this->grid;
    }

    public function afterGridCollectionLoad()
    {
        #subtotal collection
        $totals = $this->getTotals();

        if ($totals && $totals != $this->grid->getTotals()) {
            $this->grid->setFilterTotals($totals);
            $this->grid->setFilterCountTotals(1);
        }

        return $this;
    }

    /**
     * @return Mirasvit_Advr_Block_Adminhtml_Block_Chart_Abstract
     */
    protected function initChart()
    {
        $blockType = 'advr/adminhtml_block_chart_' . $this->getChartType();

        $this->chart = Mage::app()->getLayout()->createBlock($blockType);

        $this->chart
            ->setCollection($this->getCollection($this->getFilterData()))
            ->setColumns($this->grid->getColumns());

        return $this->chart;
    }

    public function getGridHtml()
    {
        if ($this->grid) {
            return $this->grid->toHtml();
        }

        return null;
    }

    public function getToolbarHtml()
    {
        if ($this->toolbar) {
            return $this->toolbar->toHtml();
        }

        return null;
    }

    public function getStoreSwitcherHtml()
    {
        if ($this->storeSwitcher) {
            return $this->storeSwitcher->toHtml();
        }

        return null;
    }

    public function getChartHtml()
    {
        if ($this->chart) {
            return $this->chart->toHtml();
        }

        return null;
    }

    public function getCollection($filterData = null)
    {
        if (!$filterData) {
            $filterData = $this->getFilterData();
        }

        $hash = md5(serialize($filterData->getData()));

        if (!$this->hasData($hash)) {
            $collection = $this->_prepareCollection();
            $this->setData($hash, $collection);
        }

        return $this->getData($hash);
    }

    public function getVisibleColumns()
    {
        $columns = array_keys($this->grid->getColumns());

        foreach ($this->grid->getColumns() as $column) {
            $columns[] = $column->getIndex();
        }

        if ($orderColumn = $this->grid->getParam($this->grid->getVarNameSort())) {
            $columns[] = $orderColumn;
        }

        $columns = array_unique(array_filter($columns));

        return $columns;
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getFilterData()
    {
        if (!$this->hasData('filter_data')) {
            $data = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
            $data = array_filter($data, array($this, 'filterNull'));

            # restore filters from cookies
            $savedData = Mage::getModel('core/cookie')->get('advr_filter_data');
            if ($savedData) {
                $savedData = Mage::helper('core')->jsonDecode($savedData);
                if (is_array($savedData)) {
                    foreach ($savedData as $key => $value) {
                        if (!isset($data[$key]) && in_array($key, array('interval', 'from', 'to', 'range'))) {
                            $data[$key] = $value;
                        }
                    }
                }
            }

            # save filters to cookies
            Mage::getModel('core/cookie')->set('advr_filter_data', Mage::helper('core')->jsonEncode($data));

            $data = $this->_filterDates($data, array('from', 'to', 'compare_from', 'compare_to'));

            $currentMonth = Mage::helper('advr/date')->getInterval(Mirasvit_Advr_Helper_Date::THIS_MONTH);

            if (!isset($data['from'])) {
                $data['from'] = $currentMonth->getFrom()->get(Varien_Date::DATETIME_INTERNAL_FORMAT);
            }

            if (!isset($data['to'])) {
                $data['to'] = $currentMonth->getTo()->get(Varien_Date::DATETIME_INTERNAL_FORMAT);
            }

            if (strpos($data['from'], ':') === false) {
                $data['from'] .= ' 00:00:00';
            }
            if (isset($data['compare_from']) && strpos($data['compare_from'], ':') === false) {
                $data['compare_from'] .= ' 00:00:00';
            }

            if (strpos($data['to'], ':') === false) {
                $data['to'] .= ' 23:59:59';
            }
            if (isset($data['compare_to']) && strpos($data['compare_to'], ':') === false) {
                $data['compare_to'] .= ' 23:59:59';
            }

            if (!isset($data['range'])) {
                $data['range'] = '1d';
            }

            if (!isset($data['group_by'])) {
                $data['group_by'] = 'status';
            }

            $offset = Mage::getModel('core/date')->timestamp() - Mage::getModel('core/date')->gmtTimestamp();

            $fromLocal = new Zend_Date(strtotime($data['from']) - $offset);
            $data['from_local'] = $fromLocal->get(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $toLocal = new Zend_Date(strtotime($data['to']) - $offset);
            $data['to_local'] = $toLocal->get(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $data['store_ids'] = array_filter(explode(',', $this->getRequest()->getParam('store_ids')));

            $data = array_filter($data);

            $this->setData('filter_data', new Varien_Object($data));

        }

        return $this->getData('filter_data');
    }

    public function getCompareFilterData()
    {
        if (!$this->getFilterData()->getCompare()) {
            return false;
        }

        $params = $this->getFilterData();
        $params->setFrom($this->getFilterData()->getCompareFrom());
        $params->setTo($this->getFilterData()->getCompareTo());

        return $params;
    }

    protected function _filterDates($array, $dateFields)
    {
        if (empty($dateFields)) {
            return $array;
        }

        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'locale'      => Mage::app()->getLocale()->getLocaleCode(),
            'date_format' => Mage::getSingleton('advr/config')->dateFormat()
        ));

        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'locale'      => Mage::app()->getLocale()->getLocaleCode(),
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
        ));

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }

        return $array;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getFilterDataAsString()
    {
        if ($this->getFilterData()->getFrom()) {
            $html[] = 'From: ' . $this->getFilterData()->getFrom();
        }

        if ($this->getFilterData()->getTo()) {
            $html[] = 'To: ' . $this->getFilterData()->getTo();
        }

        foreach ($this->getGrid()->getColumns() as $column) {
            if ($column->getFilter()) {
                $condition = $column->getFilter()->getCondition();
                if (isset($condition['from'])) {
                    $html[] = $column->getHeader() . ' from ' . $condition['from'];
                }
                if (isset($condition['to'])) {
                    $html[] = $column->getHeader() . ' to ' . $condition['to'];
                }
                if (isset($condition['like']) && $condition['like'] != "'%%'") {
                    $html[] = $column->getHeader() . ' like ' . $condition['like'];
                }
                if (isset($condition['eq'])) {
                    $html[] = $column->getHeader() . ' equal ' . $condition['eq'];
                }
            }
        }

        return implode('<br>', $html);
    }

    public function getSubHeaderText()
    {
        return $this->getFilterDataAsString();
    }

    public function filterNull($el)
    {
        if (is_array($el)) {
            return array_filter($el, array($this, 'filterNull'));
        }

        return strlen($el);
    }
}
