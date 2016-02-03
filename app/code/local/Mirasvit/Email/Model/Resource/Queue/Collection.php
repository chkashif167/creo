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


class Mirasvit_Email_Model_Resource_Queue_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('email/queue');
    }

    public function addReadyFilter()
    {
        $this->addFieldToFilter('scheduled_at', array('lt' => Mage::getSingleton('core/date')->gmtDate()))
            ->addFieldToFilter('sent_at', array('null' => true))
            ->addFieldToFilter('status', Mirasvit_Email_Model_Queue::STATUS_PENDING);

        return $this;
    }
}