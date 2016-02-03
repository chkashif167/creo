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



/**
 * Class Mirasvit_Advr_Helper_Callback
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Mirasvit_Advr_Helper_Callback
{
    public function period($value, $row, $column)
    {
        $result = array();

        $values = explode('|', $value);

        foreach ($values as $val) {
            $strVal  = strtotime($val);
            switch ($column->getGrid()->getFilterData()->getRange()) {
                default:
                case '1d':
                    $result[] = date('d M, Y', $strVal);
                    break;

                case '1w':
                    $result[] = date('d M, Y', $strVal - 7 * 24 * 60 * 60)
                        . ' – '
                        . date('d M, Y', $strVal)
                        . ' (' . (date('W', $strVal) - 1) . ')';
                    break;

                case '1m':
                    $result[] = date('M, Y', $strVal);
                    break;

                case '1q':
                    $year    = date('Y', $strVal);
                    switch (date('n', $strVal)) {
                        case 1:
                            $result[] = 'Jan, ' . $year.' – Mar, ' . $year;
                            break;
                        case 2:
                            $result[] = 'Apr, ' . $year.' – Jun, ' . $year;
                            break;
                        case 3:
                            $result[] = 'Jul, ' . $year.' – Sep, ' . $year;
                            break;
                        case 4:
                            $result[] = 'Oct, ' . $year.' – Dec, ' . $year;
                            break;
                    }
                    break;

                case '1y':
                    $result[] = date('Y', $strVal);
            }
        }

        return implode('<br>', $result);
    }

    public function hour($value, $row, $column)
    {
        if (strlen($value) == 1) {
            $value = '0' . $value;
        }

        return $value . ':00';
    }

    public function day($value, $row, $column)
    {
        $value += 1;

        return date('D', strtotime("Sunday +$value days"));
    }

    public function time($value, $row, $column)
    {
        $mins = floor(($value % 3600) / 60);
        $hours = floor(($value % 86400) / 3600);
        $days = floor(($value % 2592000) / 86400);
        $months = floor($value / 2592000);

        $output = array();

        if ($months > 0) {
            $output [] = "$months " . ngettext('month', 'months', $months);
        }

        if ($days > 0) {
            $output [] = "$days " . ngettext('day', 'days', $days);
        }

        if ($hours > 0) {
            $output [] = "$hours " . ngettext('hour', 'hours', $hours);
        }

        if ($mins > 0) {
            $output [] = "$mins " . ngettext('min', 'mins', $mins);
        }

        return implode(' ', $output);
    }

    public function _percent($value, $row, $column)
    {
        $totals = $column->getGrid()->getTotals();

        $total = $totals->getData($column->getIndex()) ? $totals->getData($column->getIndex()) : 1;

        $result = $value / $total * 100;

        return sprintf("%.1f", $result);
    }

    public function percent($value, $row, $column)
    {
        return sprintf("%.1f %%", $this->_percent($value, $row, $column));
    }

    public function percentOf($value, $row, $column)
    {
        $of = $row->getData($column->getPercentOf());

        $of = $of ? $of : 1;

        $result = $value / $of;

        return '&nbsp;<small class="discount">' . sprintf("%.1f %%", $result * 100) . '</small>' . $value;
    }

    public function discount($value, $row, $column)
    {
        $from = $row->getData($column->getData('discount_from'));
        $discount = $row->getData($column->getIndex());

        $percent = 0;
        if ($from > 0) {
            $percent = round($discount / $from * 100, 2);
        }

        if (abs($percent) > 100) {
            $width = 100;
        } else {
            $width = abs($percent);
        }

        return '<div class="percent-bar" style="width: ' . abs($width) . '%;"></div>
            <div class="percent-value"><small class="discount">' . $percent . '%</small>
            &nbsp;&nbsp;&nbsp;' . $value . '</div>';
    }

    public function country($value, $row, $column)
    {
        $value = $row->getCountryId();

        $img = '';
        if ($value) {
            $img = '<img style="height:13px;width:19px;"
                src="http://www.geonames.org/flags/x/' . strtolower($value) . '.gif">&nbsp;&nbsp;&nbsp;#';
        }
        return $img . Mage::app()->getLocale()->getCountryTranslation($value);
    }

    public function region($value, $row, $column)
    {
        if (intval($value) > 0) {
            $value = Mage::getModel('directory/region')->load($value)->getName();
        }

        return $value;
    }

    public function paymentMethod($value, $row, $column)
    {
        $methods = $this->_getPaymentMethods();
        if (isset($methods[$value])) {
            $value = $methods[$value];
        }
        $value = strip_tags($value);

        return $value;
    }

    protected function _getPaymentMethods()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $methods = array();
        foreach (array_keys($payments) as $paymentCode) {
            $paymentTitle = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
            $methods[$paymentCode] = $paymentTitle;
        }

        return $methods;
    }

    public function category($value, $row, $column)
    {
        $level = (int)$row->getCategoryLevel();

        $value = str_repeat('&nbsp;', $level * 5) . $value;

        return $value;
    }

    public function multiCategory($value, $row, $column)
    {
        $result = array();
        $value = explode(',', $row->getData($column->getIndex()));
        foreach ($column->getOptions() as $val => $label) {
            if (in_array($val, $value)) {
                $result[] = str_replace('-', '', $label);
            }
        }

        return implode(', ', $result);
    }

    public function categoryPath($value, $row, $column)
    {
        $path = explode('/', $value);
        if (($key = array_search(1, $path)) !== false) {
            unset($path[$key]);
        }
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')
            ->addFieldToFilter('entity_id', array('in' => $path));
        $collection->getSelect()->order('level ASC');

        if ($collection->count()) {
            $fullPath = array();
            foreach ($collection as $category) {
                $fullPath[] = $category->getName();
            }
            $value = implode(' / ', $fullPath);
        }

        return $value;
    }

    public function linkToCustomer($value, $row, $column)
    {
        $link = Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId()));

        return '<a href="' . $link . '">' . $value . '</a>';
    }

    public function rowUrl($url, $row, $filters = array())
    {
        $filter = array();
        foreach ($filters as $field) {
            if ($field == 'period') {
                $period = explode('|', $row->getData('period'));
                $periodFrom = strtotime($period[0]);
                $periodTo = $periodFrom;

                switch ($row->getData('range')) {
                    case '1w':
                        $periodTo += 7 * 24 * 60 * 60;
                        break;

                    case '1m':
                        $periodTo += 30 * 24 * 60 * 60;
                        break;

                    case '1q':
                        $periodTo += 80 * 24 * 60 * 60;
                        break;

                    case '1y':
                        $periodTo += 365 * 24 * 60 * 60;
                }

                $format = Mage::getSingleton('advr/config')->dateFormat();

                $from = new Zend_Date($periodFrom, null, Mage::app()->getLocale()->getLocaleCode());
                $to = new Zend_Date($periodTo, null, Mage::app()->getLocale()->getLocaleCode());

                $filter = array(
                    'from' => $from->toString($format),
                    'to'   => $to->toString($format)
                );
            } else {
                $filter[$field] = $row->getData($field);
            }
        }

        $filter = base64_encode(http_build_query($filter));

        return Mage::helper('adminhtml')->getUrl($url, array('filter' => $filter));
    }

    public function shippingMethod($value, $row, $column)
    {
        $methods = $this->_getShippingMethods();
        foreach ($methods as $carriers) {
            foreach ($carriers['value'] as $options) {
                if ($value == $options['value']) {
                    $value = $options['label'];
                }
            }
        }

        return $value;
    }

    public function _getShippingMethods()
    {
        $methods = array();
        $activeCarriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $options = array();
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $carrierCode . '_' . $methodCode;
                    $options[] = array('value' => $code, 'label' => $method);
                }
                $carrierTitle = Mage::getStoreConfig('carriers/' . $carrierCode . '/title');
            }
            $methods[] = array('value' => $options, 'label' => $carrierTitle);
        }

        return $methods;
    }
}
