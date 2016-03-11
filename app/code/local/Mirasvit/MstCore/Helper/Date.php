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


class Mirasvit_MstCore_Helper_Date extends Mage_Core_Helper_Data
{
    public function formatDateForSave($object, $field, $format = false)
    {
        $formated = $object->getData($field . '_is_formated');
        if (!$formated && $object->hasData($field)) {
            try {
                $value = $this->_formatDateForSave($object->getData($field), $format);
            } catch (Exception $e) {
                throw Mage::exception('Mage_Core', Mage::helper('mstcore')->__('Invalid date'));
            }

            if (is_null($value)) {
                $value = $object->getData($field);
            }

            $object->setData($field, $value);
            $object->setData($field . '_is_formated', true);
        }
    }

    /**
     * Prepare date for save in DB
     *
     * string format used from input fields (all date input fields need apply locale settings)
     * int value can be declared in code (this meen whot we use valid date)
     *
     * @param   string | int $date
     * @return  string
     */
    protected function _formatDateForSave($date, $format)
    {
        if (empty($date)) {
            return null;
        }

        if ($format) {
            $date = Mage::app()->getLocale()->date($date,
               $format,
               null, false
            );
        } elseif (preg_match('/^[0-9]+$/', $date)) {
            // unix timestamp given - simply instantiate date object
            $date = new Zend_Date((int)$date);
        } else if (preg_match('#^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$#', $date)) {
            // international format
            $zendDate = new Zend_Date();
            $date     = $zendDate->setIso($date);
        } else {
            // parse this date in current locale, do not apply GMT offset
            $date = Mage::app()->getLocale()->date($date,
               Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
               null, false
            );

        }
        return $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }
}