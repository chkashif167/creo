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


// @codingStandardsIgnoreFile
require_once 'abstract.php';

class Mirasvit_Shell_Advr extends Mage_Shell_Abstract
{
    public function run()
    {
        ini_set('display_errors', 1);
        ini_set('memory_limit', '16000M');
        error_reporting(E_ALL);
        set_time_limit(36000);

        if ($this->getArg('notify')) {
            $this->notify();
        } elseif ($this->getArg('geo-copy-unknown')) {
            Mage::getSingleton('advr/postcode')->copyUnknown(true);
        } elseif ($this->getArg('geo-update')) {
            Mage::getSingleton('advr/postcode')->batchUpdate(true);
        } elseif ($this->getArg('geo-merge')) {
            Mage::getSingleton('advr/postcode')->batchMerge(true);
        } elseif ($this->getArg('geo-export')) {
            Mage::getSingleton('advr/postcode')->exportAll(true);
        } else {
            echo $this->usageHelp();
        }
    }

    protected function notify()
    {
        $emails = Mage::getModel('advd/notification')->getCollection()
            ->addFieldToFilter('is_active', 1);

        foreach ($emails as $email) {
            $email = $email->load($email->getId());
            $email->send();

            echo $email->getRecipientEmail() . ' OK' . PHP_EOL;
        }
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f advr.php -- [options]

  --notify               Send notifications to all subscribed users
  --geo-import           Import post codes from file to database
  --geo-export           Export post codes from database to file
  --geo-copy-unknown     Import post codes (in shipping address) to post codes table
  --geo-update           Fetch information for post codes without information
  --geo-merge            Update post codes without information

USAGE;
    }
}

$shell = new Mirasvit_Shell_Advr();
$shell->run();

