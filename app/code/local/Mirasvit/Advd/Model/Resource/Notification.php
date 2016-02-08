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



class Mirasvit_Advd_Model_Resource_Notification extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('advd/notification', 'notification_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        if (!$object->hasUserId()) {
            $object->setUserId($object->getCurrentUser()->getId());
        }

        if (is_array($object->getScheduleDay())) {
            $object->setScheduleDay(implode(',', $object->getScheduleDay()));
        }
        if (is_array($object->getScheduleTime())) {
            $object->setScheduleTime(implode(',', $object->getScheduleTime()));
        }
        if (is_array($object->getEmailWidgets())) {
            $object->setEmailWidgets(implode(',', $object->getEmailWidgets()));
        }

        return parent::_beforeSave($object);
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object
            ->setScheduleDay(explode(',', $object->getScheduleDay()))
            ->setScheduleTime(explode(',', $object->getScheduleTime()))
            ->setEmailWidgets(explode(',', $object->getEmailWidgets()));

        return parent::_afterLoad($object);
    }
}
