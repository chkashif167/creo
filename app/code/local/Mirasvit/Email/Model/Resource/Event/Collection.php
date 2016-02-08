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


class Mirasvit_Email_Model_Resource_Event_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('email/event');
    }

    public function addNewFilter($triggerId, $storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds[] = $storeIds;
        }

        $this->getSelect()
            ->joinLeft(array('et' => $this->getTable('email/event_trigger')),
                "et.`event_id` = main_table.`event_id` AND et.`trigger_id` = $triggerId",
                array())
            ->where('(et.`status` = "new" OR et.`status` IS NULL)');

        if (count($storeIds) && !in_array(0, $storeIds)) {
            $this->getSelect()->where('(main_table.`store_ids` IN ('.implode(',', $storeIds).') OR main_table.`store_ids` = 0)');
        }

        return $this;
    }
}