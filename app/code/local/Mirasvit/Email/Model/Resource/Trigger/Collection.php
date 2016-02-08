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


class Mirasvit_Email_Model_Resource_Trigger_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('email/trigger');
    }

    public function addActiveFilter()
    {
        $date = Mage::app()->getLocale()->date();

        $activeFrom   = array();
        $activeFrom[] = array('date' => true, 'to' => date($date->toString('YYYY-MM-dd H:mm:ss')));
        $activeFrom[] = array('date' => true, 'from' => '0000-00-00 00:00:00');
        $activeFrom[] = array('date' => true, 'null' => true);

        $activeTo     = array();
        $activeTo[]   = array('date' => true, 'from' => date($date->toString('YYYY-MM-dd H:mm:ss')));
        $activeTo[]   = array('date' => true, 'from' => '0000-00-00 00:00:00');
        $activeTo[]   = array('date' => true, 'null' => true);

        $this->addFieldToFilter('is_active', 1);
        $this->addFieldToFilter('active_from', $activeFrom);
        $this->addFieldToFilter('active_to', $activeTo);

        return $this;
    }

    public function addEventFilter($value)
    {
        $this->addFieldToFilter('event', $value);

        return $this;
    }

    public function addEventOrFilter($value)
    {
        $this->getSelect()->where('find_in_set(?, cancellation_event) OR event=?', $value, $value);

        return $this;
    }

    public function addCancellationEventFilter($value)
    {
        $this->getSelect()->where('find_in_set(?, cancellation_event)', $value);

        return $this;
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('trigger_id', 'title');
    }

    public function addItem(Varien_Object $item)
    {
        $item->setStoreIds(explode(',', $item->getStoreIds()));

        return parent::addItem($item);
    }
}