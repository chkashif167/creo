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


class Mirasvit_EmailReport_Model_Resource_Recipient_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('email/queue');
    }

    public function getSingleRecipientCollection()
    {
        $select = $this->getSelect();

        $select->columns(array('emails_num' => new Zend_Db_Expr('COUNT(queue_id)')));

        $delivered = new Zend_Db_Expr('(SELECT COUNT(eqd.queue_id) FROM '.$this->getTable('email/queue').' eqd WHERE eqd.recipient_email = main_table.recipient_email AND eqd.status = "'.Mirasvit_Email_Model_Queue::STATUS_DELIVERED.'")');
        $select->columns(array('emails_num_delivered' => $delivered));

        $pending = new Zend_Db_Expr('(SELECT COUNT(eqp.queue_id) FROM '.$this->getTable('email/queue').' eqp WHERE eqp.recipient_email = main_table.recipient_email AND eqp.status = "'.Mirasvit_Email_Model_Queue::STATUS_PENDING.'")');
        $select->columns(array('emails_num_pending' => $pending));

        $open = new Zend_Db_Expr('(
            SELECT COUNT(o.open_id)
                FROM '.$this->getTable('emailreport/open').' o
                LEFT JOIN '.$this->getTable('email/queue').' q
                    ON o.queue_id = q.queue_id
                WHERE q.recipient_email = main_table.recipient_email
        )');

        $select->columns(array('open_num' => $open));

        $click = new Zend_Db_Expr('(
            SELECT COUNT(c.click_id)
                FROM '.$this->getTable('emailreport/click').' c
                LEFT JOIN '.$this->getTable('email/queue').' q
                    ON c.queue_id = q.queue_id
                WHERE q.recipient_email = main_table.recipient_email
        )');

        $select->columns(array('click_num' => $click));

        $select->group('recipient_email');

        return $this;
    }

    public function getTrendReport()
    {
        $time = time() - 365 * 24 * 60 * 60;

        $result = array();
        $adapter = $this->getConnection();

        $statuses = array(
            Mirasvit_Email_Model_Queue::STATUS_PENDING,
            Mirasvit_Email_Model_Queue::STATUS_DELIVERED,
            Mirasvit_Email_Model_Queue::STATUS_CANCELED,
            Mirasvit_Email_Model_Queue::STATUS_UNSUBSCRIBED,
            Mirasvit_Email_Model_Queue::STATUS_ERROR,
            Mirasvit_Email_Model_Queue::STATUS_MISSED,
        );

        foreach ($statuses as $status) {
            $period = $adapter->getDateFormatSql('scheduled_at', '%Y-%m');
            $num = new Zend_Db_Expr('COUNT(queue_id)');
            $select  = clone $this->getSelect();

            $select->setPart('columns', array())
                ->columns(array('period' => $period))
                ->columns(array('num' => $num))
                ->where('status = ?', $status)
                ->where('created_at > ?', date(DateTime::ISO8601, $time))
                ->group('period');

            $pairs = $adapter->fetchPairs($select);

            for ($i = $time; $i < time(); $i += 30 * 24 * 60 * 60) {
                $dy = date('Y-m', $i);
                $result[$dy][$status] = 0;
                if (isset($pairs[$dy])) {
                    $result[$dy][$status] = $pairs[$dy];
                }
            }
        }
        return $result;
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