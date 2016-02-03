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


class Mirasvit_EmailReport_Model_Resource_Email_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('email/trigger');
    }

    public function getTriggerReport()
    {
        $select = $this->getSelect();

        $sent = new Zend_Db_Expr('(
            SELECT COUNT(q.queue_id)
            FROM '.$this->getTable('email/queue').' q
            WHERE
                q.trigger_id = main_table.trigger_id
                AND q.status = "'.Mirasvit_Email_Model_Queue::STATUS_DELIVERED.'")'
        );
        $select->columns(array('sent' => $sent));

        $open = new Zend_Db_Expr('(
            SELECT COUNT(o.open_id)
                FROM '.$this->getTable('emailreport/open').' o
                LEFT JOIN '.$this->getTable('email/queue').' q
                    ON o.queue_id = q.queue_id
                WHERE q.trigger_id = main_table.trigger_id
        )');

        $select->columns(array('opens' => $open));

        $readers = new Zend_Db_Expr('(
            SELECT COUNT(DISTINCT(o.queue_id))
                FROM '.$this->getTable('emailreport/open').' o
                LEFT JOIN '.$this->getTable('email/queue').' q
                    ON o.queue_id = q.queue_id
                WHERE q.trigger_id = main_table.trigger_id
        )');

        $select->columns(array('readers' => $readers));

        $clicks = new Zend_Db_Expr('(
            SELECT COUNT(DISTINCT(c.queue_id))
                FROM '.$this->getTable('emailreport/click').' c
                LEFT JOIN '.$this->getTable('email/queue').' q
                    ON c.queue_id = q.queue_id
                WHERE q.trigger_id = main_table.trigger_id
        )');

        $select->columns(array('unique_clicks' => $clicks));

        $unsubscribed = new Zend_Db_Expr('(
            SELECT COUNT(q.queue_id)
            FROM '.$this->getTable('email/queue').' q
            WHERE
                q.trigger_id = main_table.trigger_id
                AND q.status = "'.Mirasvit_Email_Model_Queue::STATUS_UNSUBSCRIBED.'")'
        );
        $select->columns(array('unsubscribed' => $unsubscribed));

        return $this;
    }

    public function getSelectCountSql()
    {
        $this->_renderFilters();

        /* @var Varien_Db_Select $select */
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->resetJoinLeft();
        $select->columns(new Zend_Db_Expr('1'));

        /* @var Varien_Db_Select $countSelect */
        $countSelect = clone $select;
        $countSelect->reset();
        $countSelect->from($select, "COUNT(*)");

        return $countSelect;
    }
}