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



class Mirasvit_Advr_Model_Config
{
    protected static $usedColors = array();
    protected static $colorIdx = 0;

    public function dateFormat()
    {
        return Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

    public function calendarDateFormat()
    {
        return Varien_Date::convertZendToStrFtime($this->dateFormat());
    }

    public function getCurrencyFormat()
    {
        $format = Mage::app()->getLocale()->getJsPriceFormat();

        return str_replace('%s', '#', $format['pattern']);

        return str_replace('9', '#', Mage::helper('core')->currency(999999.99, true, false));
    }

    public function isLinkUnderReport()
    {
        return Mage::getStoreConfig('advr/view/link_under_report');
    }

    public function isReplaceDashboardLink()
    {
        return Mage::getStoreConfig('advr/view/replace_dashboard_link');
    }

    public function getChartColumnColor($code)
    {
        $color = false;

        $colors = array(
            "#2fd75b",
            "#ff7e0e",
            "#52c4ff",
            "#c7b700",
            "#aaeeee",
            "#ff0066",
            "#eeaaee",
            "#55BF3B",
            "#DF5353",
            "#7798BF",
            "#aaeeee",
            '#602E0A'
        );

        if (strpos($code, 'grand_total') || strpos($code, 'row_total')) {
            $color = '#2fd75b';
        } elseif (strpos($code, 'subtotal')) {
            $color = '#ff7e0e';
        } elseif (strpos($code, 'shipping')) {
            $color = '#c7b700';
        } elseif (strpos($code, 'refund')) {
            $color = '#ff0066';
        }

        if (!$color) {
            $color = $colors[substr(crc32($code), 0, 1)];
        }

        while (in_array($color, self::$usedColors)) {
            self::$colorIdx++;
            if (self::$colorIdx >= count($colors)) {
                break;
            }

            $color = $colors[self::$colorIdx];
        }

        self::$usedColors[] = $color;

        return $color;
    }

    public function getProcessOrderStatuses()
    {
        $statuses = explode(',', Mage::getStoreConfig('advr/report/process_orders'));
        $statuses = array_filter($statuses);

        if (!count($statuses)) {
            $statuses[] = 'complete';
        }

        return $statuses;
    }

    public function getGeoFilesPath()
    {
        $dir = Mage::getBaseDir('media') . DS . 'advr';
        if (!file_exists($dir)) {
            mkdir($dir);
        }

        return $dir;
    }
}
