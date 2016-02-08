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



class Mirasvit_Advd_Block_Adminhtml_Widget_Order_Country extends Mirasvit_Advd_Block_Adminhtml_Widget_Abstract_Chart
{
    public function getGroup()
    {
        return 'Sales';
    }

    public function getName()
    {
        return 'Sales By Country (Pie Chart)';
    }

    public function _prepareLayout()
    {
        $this->setTemplate('mst_advd/widget/chart/pie.phtml');

        return $this;
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

        return $this;
    }

    protected function _getCollection()
    {
        if (!$this->hasData('collection')) {
            $interval = Mage::helper('advr/date')->getInterval($this->getParam('interval'), true);
            $filterData = new Varien_Object(array(
                'from'      => $interval->getFrom()->get(Varien_Date::DATETIME_INTERNAL_FORMAT),
                'to'        => $interval->getTo()->get(Varien_Date::DATETIME_INTERNAL_FORMAT),
                'store_ids' => $this->getParam('store_ids')
            ));

            $collection = Mage::getModel('advr/report_sales')
                ->setBaseTable('sales/order')
                ->selectColumns(array('country_id', 'sum_grand_total'))
                ->setFilterData($filterData)
                ->groupByColumn('country_id');

            $collection = array_reverse($collection->getItems());

            $this->setData('collection', $collection);
        }

        return $this->getData('collection');
    }

    public function getCategories()
    {
        $result = array();
        $collection = $this->_getCollection();

        foreach ($collection as $item) {
            $result[] = $item->getCountry();
        }

        return $result;
    }

    public function getSeries()
    {
        $series = array();

        $series[] = array('Country', 'Grand Total');

        foreach ($this->_getCollection() as $itm) {
            $series[] = array(
                Mage::app()->getLocale()->getCountryTranslation($itm->getCountryId()),
                floatval($itm->getSumGrandTotal()),
            );
        }

        return $series;
    }
}
