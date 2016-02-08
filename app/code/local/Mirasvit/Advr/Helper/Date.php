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



class Mirasvit_Advr_Helper_Date extends Mage_Core_Helper_Abstract
{
    const TODAY = 'today';
    const YESTERDAY = 'yesterday';
    const THIS_WEEK = 'week';
    const PREVIOUS_WEEK = 'prev_week';
    const THIS_MONTH = 'month';
    const PREVIOUS_MONTH = 'prev_month';
    const THIS_QUARTER = 'quarter';
    const PREVIOUS_QUARTER = 'prev_quarter';
    const THIS_YEAR = 'year';
    const PREVIOUS_YEAR = 'prev_year';

    const LAST_24H = 'last_24h';
    const LAST_7D = 'last_7d';
    const LAST_30D = 'last_30d';
    const LAST_3M = 'last_3m';
    const LAST_12M = 'last_12m';

    const LIFETIME = 'lifetime';
    const CUSTOM = 'custom';

    public function getIntervals($subIntervals = false, $lifetime = false, $custom = false)
    {
        $intervals = array();

        $intervals[self::TODAY] = 'Today';
        $intervals[self::YESTERDAY] = 'Yesterday';

        $intervals[self::THIS_WEEK] = 'This week';
        $intervals[self::PREVIOUS_WEEK] = 'Previous week';

        $intervals[self::THIS_MONTH] = 'This month';
        $intervals[self::PREVIOUS_MONTH] = 'Previous month';

        $intervals[self::THIS_QUARTER] = 'This quarter';
        $intervals[self::PREVIOUS_QUARTER] = 'Previous quarter';

        $intervals[self::THIS_YEAR] = 'This year';
        $intervals[self::PREVIOUS_YEAR] = 'Previous year';

        if ($subIntervals) {
            $intervals[self::LAST_24H] = 'Last 24h hours';
            $intervals[self::LAST_7D] = 'Last 7 days';
            $intervals[self::LAST_30D] = 'Last 30 days';
            $intervals[self::LAST_3M] = 'Last 3 months';
            $intervals[self::LAST_12M] = 'Last 12 months';
        }

        if ($lifetime) {
            $intervals[self::LIFETIME] = 'Lifetime';
        }

        if ($custom) {
            $intervals[self::CUSTOM] = 'Custom';
        }

        foreach ($intervals as $code => $label) {
            $label = Mage::helper('advd')->__($label);

            $hint = $this->getIntervalHint($code);

            if ($hint) {
                $label .= ' / ' . $hint;
            }

            $intervals[$code] = $label;
        }

        return $intervals;
    }

    public function getIntervalHint($code)
    {
        $interval = $this->getInterval($code, true);
        $from = $interval->getFrom();
        $to = $interval->getTo();

        switch ($code) {
            case self::TODAY:
            case self::YESTERDAY:
                $hint = $from->get('MMM, d');
                break;

            case self::THIS_MONTH:
            case self::PREVIOUS_MONTH:
                $hint = $from->get('MMM');
                break;
            
            case self::THIS_YEAR:
            case self::PREVIOUS_YEAR:
                $hint = $from->get('YYYY');
                break;

            case self::LAST_24H:
                $hint = $from->get('MMM, d HH:mm') . ' - ' . $to->get('MMM, d HH:mm');
                break;

            default:
                $hint = $from->get('MMM, d') . ' - ' . $to->get('MMM, d');
        }

        return $hint;
    }

    public function getIntervalsAsOptions($subintervals = false, $lifetime = false, $custom = false)
    {
        $intervals = $this->getIntervals($subintervals, $lifetime, $custom);
        $options = array();

        foreach ($intervals as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }

        return $options;
    }

    /**
     * Return interval (two GMT Zend_Date)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getInterval($code, $timezone = false)
    {
        $timestamp = Mage::getSingleton('core/date')->gmtTimestamp();
        $firstDay = (int)Mage::getStoreConfig('general/locale/firstday');

        if ($timezone) {
            $timestamp = Mage::app()->getLocale()->date($timestamp);
        }

        $from = new Zend_Date(
            $timestamp,
            null,
            Mage::app()->getLocale()->getLocaleCode()
        );
        $to = clone $from;

        switch ($code) {
            case self::TODAY:
                $from->setTime('00:00:00');

                $to->setTime('23:59:59');

                break;

            case self::YESTERDAY:
                $from->subDay(1)
                    ->setTime('00:00:00');

                $to->subDay(1)
                    ->setTime('23:59:59');

                break;

            case self::THIS_MONTH:
                $from->setDay(1)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->addDay($to->get(Zend_Date::MONTH_DAYS) - 1)
                    ->setTime('23:59:59');

                break;

            case self::PREVIOUS_MONTH:
                $from->setDay(1)
                    ->subMonth(1)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->subMonth(1)
                    ->addDay($to->get(Zend_Date::MONTH_DAYS) - 1)
                    ->setTime('23:59:59');

                break;

            case self::THIS_QUARTER:
                $month = intval($from->get(Zend_Date::MONTH) / 4) * 3 + 1;
                $from->setDay(1)
                    ->setMonth($month)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->setMonth($month)
                    ->addMonth(3)
                    ->subDay(1)
                    ->setTime('23:59:59');

                break;

            case self::PREVIOUS_QUARTER:
                $month = intval($from->get(Zend_Date::MONTH) / 4) * 3 + 1;

                $from->setDay(1)
                    ->setMonth($month)
                    ->subMonth(3)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->setMonth($month)
                    ->addMonth(3)
                    ->subDay(1)
                    ->subMonth(3)
                    ->setTime('23:59:59');

                break;

            case self::THIS_YEAR:
                $from->setDay(1)
                    ->setMonth(1)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->setMonth(1)
                    ->addDay($to->get(Zend_Date::LEAPYEAR) ? 365 : 364)
                    ->setTime('23:59:59');

                break;

            case self::PREVIOUS_YEAR:
                $from->setDay(1)
                    ->setMonth(1)
                    ->subYear(1)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->setMonth(1)
                    ->addDay($to->get(Zend_Date::LEAPYEAR) ? 365 : 364)
                    ->subYear(1)
                    ->setTime('23:59:59');

                break;

            case self::LAST_24H:
                $from->subDay(1);

                break;

            case self::THIS_WEEK:
                $weekday = $from->get(Zend_Date::WEEKDAY_DIGIT); #0-6

                if ($weekday < $firstDay) {
                    $weekday += 7;
                }

                $from->subDay($weekday - $firstDay)
                    ->setTime('00:00:00');

                $to->addDay(6 - $weekday + $firstDay)
                    ->setTime('23:59:59');

                break;

            case self::PREVIOUS_WEEK:
                $weekday = $from->get(Zend_Date::WEEKDAY_DIGIT); #0-6

                if ($weekday < $firstDay) {
                    $weekday += 7;
                }

                $from->subDay($weekday - $firstDay)
                    ->subWeek(1)
                    ->setTime('00:00:00');

                $to->addDay(6 - $weekday + $firstDay)
                    ->subWeek(1)
                    ->setTime('23:59:59');

                break;

            case self::LAST_7D:
                $from->subDay(7);

                break;

            case self::LAST_30D:
                $from->subDay(30);

                break;

            case self::LAST_3M:
                $from->subMonth(3);

                break;

            case self::LAST_12M:
                $from->subYear(1);

                break;

            case self::LIFETIME:
                $from->subYear(10);

                $to->addYear(10);

                break;
        }

        return new Varien_Object(array(
            'from' => $from,
            'to'   => $to));
    }

    public function getPreviousInterval($code, $offsetDays = 0, $timezone = false)
    {
        $interval = $this->getInterval($code, $timezone);

        $now = new Zend_Date(
            Mage::getSingleton('core/date')->gmtTimestamp(),
            null,
            Mage::app()->getLocale()->getLocaleCode()
        );

        $diff = clone $interval->getTo();
        $diff->sub($interval->getFrom());

        if ($timezone) {
            $diff->sub(Mage::getSingleton('core/date')->getGmtOffset());
        }

        if ($interval->getTo()->getTimestamp() > $now->getTimestamp()) {
            $interval->getTo()->subTimestamp($interval->getTo()->getTimestamp() - $now->getTimestamp());
        }

        if (intval($offsetDays) > 0) {
            $interval->getFrom()->subDay($offsetDays);
            $interval->getTo()->subDay($offsetDays);
        } else {
            $interval->getFrom()->sub($diff);
            $interval->getTo()->sub($diff);
        }

        return $interval;
    }
}
