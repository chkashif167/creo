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


class Mirasvit_MstCore_Helper_Cron extends Mage_Core_Helper_Data
{
    /**
     * Method allows to display message about not working cron job in admin panel.
     * Need call at start of adminhtml controller action.
     * @param  string  $jobCode cronjob code (from config.xml)
     * @param  boolean $output  by default - return cron error as adminhtml error message, otherwise - as string
     * @param  string  $prefix  additonal text to cronjob error message
     */
 	public function checkCronStatus($jobCode, $output = true, $prefix = '')
    {
    	if (!$this->isCronRunning($jobCode)) {
            $message = '';
            if ($prefix) {
                $message .= $prefix.' ';
            }
            
    		$message .= $this->__('Cron for magento is not running. 
                To setup a cron job follow the 
                <a href="https://mirasvit.com/doc/common/cron?magento_path=%s&php_path=%s" target="_blank"><b>link</b></a>.',
                Mage::getBaseDir(),
                $this->getPhpBin());

            if ($output) {
                Mage::getSingleton('adminhtml/session')->addError($message);
            } else {
                return $message;
            }
    	}

        return true;
	}

 	public function isCronRunning($jobCode)
    {
        $collection = Mage::getModel('cron/schedule')->getCollection();
        if ($jobCode) {
            $collection->addFieldToFilter('job_code', $jobCode);
        }
        $collection
            ->addFieldToFilter('status', 'success')
            ->setOrder('scheduled_at', 'desc')
            ->setPageSize(1)
            ;

        $job = $collection->getFirstItem();
        if (!$job->getId()) {
            return false;
        }

        $jobTimestamp = strtotime($job->getExecutedAt());
        $timestamp    = Mage::getSingleton('core/date')->gmtTimestamp();

        if (abs($timestamp - $jobTimestamp) > 6 * 60 * 60) {
            return false;
        }

        return true;
    }

    public function getCronExpression()
    {
        $phpBin = $this->getPhpBin();
        $root   = Mage::getBaseDir();
        $var    = Mage::getBaseDir('var');

        $line = '* * * * * date >> '.$var.DS.'log'.DS.'cron.log;'
            .$phpBin.' -f '.$root.DS.'cron.php >> '.$var.DS.'log'.DS.'cron.log 2>&1;';

        return $line;
    }

    public function getPhpBin()
    {
        $phpBin = 'php';

        if (PHP_BINDIR) {
            $phpBin = PHP_BINDIR.DS.'php';
        }

        return $phpBin;
    }
}
