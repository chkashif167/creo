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


class Mirasvit_FeedExport_Block_Adminhtml_Report_Chart extends Mage_Core_Block_Template
{
    public function getChartData()
    {
        $collection     = $this->getGrid()->getCollection();
        $data           = array();
        $previousPeriod = false;

        foreach ($collection as $row) {
            $period =  $row->getPeriod();

            $previousPeriod = $period;

            $period = explode('-', $period);
            if (count($period) != 3) {
                $this->setMessage(__('Please, select period equal to day to view performance graph.'));
                return false;
            }
            $period[1] = $period[1] - 1; //january is a 0 month
            $clicks = (int) $row->getClicks();
            $orders = (int) $row->getOrders();

            $data[] = "[new Date({$period[0]}, {$period[1]} ,{$period[2]}),$clicks,$orders]";
        }

        return $data;
    }

    public function getCollection()
    {
        return $collection = $this->getGrid()->getCollection();
    }
}