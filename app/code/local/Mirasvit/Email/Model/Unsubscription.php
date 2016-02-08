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


class Mirasvit_Email_Model_Unsubscription extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('email/unsubscription');
    }

    public function unsubscribe($email, $triggerId = null)
    {
        $triggerId = intval($triggerId);

        $item = $this->getCollection()
            ->addFieldToFilter('email', $email)
            ->addFieldToFilter('trigger_id', array(0, $triggerId))
            ->getFirstItem();

        if (!$item->getId() || $triggerId == 0) {
            $item->setTriggerId($triggerId);
        }

        $item->setEmail($email);
        
        $item->save();

        return true;
    }

    public function unsubscribeNewsletter($email)
    {
        Mage::getModel('newsletter/subscriber')->loadByEmail($email)->unsubscribe();

        return true;
    }

    public function isUnsubscribed($email, $triggerId)
    {
        $item = $this->getCollection()
            ->addFieldToFilter('email', $email)
            ->addFieldToFilter('trigger_id', array(0, $triggerId))
            ->getFirstItem();

        if ($item->getId()) {
            return true;
        }

        return false;
    }
}