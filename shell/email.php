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


require_once 'abstract.php';

class Mirasvit_Shell_Email extends Mage_Shell_Abstract
{

    public function run()
    {
        if ($this->getArg('send-queue')) {
            Mage::getModel('email/observer')->sendQueue();
        } else if ($this->getArg('check-events')) {
            Mage::getModel('email/observer')->checkEvents();
        } else if ($this->getArg('reset-database')) {
            $this->resetDatabase();
        } else {
            echo $this->usageHelp();
        }
    }

    public function resetDatabase()
    {
        $resource = Mage::getSingleton('core/resource');
        $write    = $resource->getConnection('core_write');

        $queueTable    = $resource->getTableName('email/queue');
        $eventTable    = $resource->getTableName('email/event');
        $variableTable = $resource->getTableName('core/variable_value');

        $write->query("DELETE FROM $queueTable");
        $write->query("DELETE FROM $eventTable");
        $write->query("UPDATE $variableTable SET plain_value = 100");
    }

    public function _validate()
    {
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f email.php -- [options]
  send-queue        Send current queue
  check-events      Check new events
  reset-database    Clear extenstion tables (events and queue)
  help              This help

USAGE;
    }
}

$shell = new Mirasvit_Shell_Email();
$shell->run();