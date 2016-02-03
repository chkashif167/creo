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



class Mirasvit_Advd_Block_Adminhtml_Widget_Order_Status extends Mirasvit_Advd_Block_Adminhtml_Widget_Abstract_Chart
{
    public function getGroup()
    {
        return 'Sales';
    }

    public function getName()
    {
        return 'Orders by status';
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
                ->selectColumns(array('order_status', 'order_status_label', 'quantity'))
                ->setFilterData($filterData, false)
                ->groupByColumn('order_status');

            $this->setData('collection', $collection);
        }

        return $this->getData('collection');
    }

    public function getCategories()
    {
        $result = array();
        $collection = $this->_getCollection();

        foreach ($collection as $item) {
            $result[] = strtotime($item->getPeriod());
        }

        return $result;
    }

    public function getSeries()
    {
        $i = 0;
        $series = array(array('Status', 'Qty', 'URL'));

        foreach (Mage::getModel('sales/order_status')->getResourceCollection() as $status) {
            $i++;
            $series[$i] = array($status->getLabel(), 0, $this->_getStatusUrl($status->getStatus()));
            foreach ($this->_getCollection() as $item) {
                if ($status->getStatus() == $item->getOrderStatus()) {
                    $series[$i][1] = floatval($item->getQuantity());
                }
            }
        }

        return $series;
    }

    protected function _getStatusUrl($status)
    {
        $interval = Mage::helper('advr/date')->getInterval($this->getParam('interval'), true);
        $format = Mage::getSingleton('advr/config')->dateFormat();

        $filter = array(
            'from'   => $interval->getFrom()->toString($format),
            'to'     => $interval->getTo()->toString($format),
            'status' => $status
        );

        $filter = base64_encode(http_build_query($filter));

        return $this->getUrl('adminhtml/advr_order/plain', array('filter' => $filter));
    }
}
