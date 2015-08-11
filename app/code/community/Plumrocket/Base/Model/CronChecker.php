<?php 

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

@package    Plumrocket_Base-v1.x.x
@copyright  Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
@license    http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/


class Plumrocket_Base_Model_CronChecker
{
	public function check($name)
	{
		if (!$name) {
			return;
		}

		if(!$this->getCronStatus('plumrocket_'.$name.'_')) {
			$session = Mage::getSingleton('adminhtml/session');
			$message = Mage::helper('adminhtml')->__('Notice: You can disregard this message if the extension was installed less than 30 minutes ago.<br/>Magento Cron Job is missing in your crontab. This magento extension requires to schedule custom tasks to be run periodically. Please read <a href="%s" target="_blank">How to setup a Cron Job in Magento</a> for more info.', 'http://wiki.plumrocket.com/wiki/How_to_setup_a_Cron_Job_in_Magento');
			$session->addUniqueMessages(Mage::getSingleton('core/message')->notice($message)->setIdentifier('plumrocket_cronChecker'));
		}
	}


	public function getCronStatus($jobCodePrefix)
    {
    	// Get cron jobs.
    	$cronJobs = Mage::getConfig()->getNode('crontab/jobs')->asArray();

		foreach ($cronJobs as $key => &$value) {
			if(0 === strpos($key, $jobCodePrefix) && !empty($value['schedule']['cron_expr'])) {
		        $offset = false;
		        $exprArr = preg_split('#\s+#', $value['schedule']['cron_expr'], null, PREG_SPLIT_NO_EMPTY);
		        if (count($exprArr) == 5) {
		        	foreach ($exprArr as $n => $expr) {
						switch($n) {
							case 0:
								// minute
								$offset = 3600;
								break;

							case 1:
								// hour
								if($period = $this->_getPeriod($expr)) {
									$offset = 3600 * $period;
								}else{
									$offset = 3600 * 24;
								}
								break;

							default:
								if($expr !== '*')
									$offset = false;
						}
		        	}
		        }

		        if($offset) {
		        	$value = $offset;
		        	continue;
		        }
			}

			unset($cronJobs[$key]);
		}

		// Cron not used.
		if(empty($cronJobs)) {
			return true;
		}

		// Check cron.
    	$time = Mage::getSingleton('core/date')->gmtTimestamp();
		$installedAt = strtotime(Mage::getConfig()->getNode('global/install/date'));

		if ($time < $installedAt + 3600) {
		    return true;
		} else {
			$cronSchedule = Mage::getSingleton('cron/schedule');

		    $collection = $cronSchedule->getCollection()
		        ->setPageSize(1);

		    $where = array();
	        foreach ($cronJobs as $jobCode => $offset) {
	        	$where[] = '(`job_code` = "'. $jobCode .'" AND `scheduled_at` >= "'. date('Y-m-d H:i:s', $time - $offset * 2) .'")';
	        }

		    $collection->getSelect()->where(implode(' OR ', $where));

		    if (count($collection)) {
		        return true;
		    }
		}

		return false;
    }

	protected function _getPeriod($expr)
    {
        $period = false;

    	if ($expr === '*') {
            $period = 1;
        }

		if(false !== strpos($expr, ',')) {
			$part = explode(',', $expr);
			if(count($part) >= 2) {
				$sub = intval($part[1] - $part[0]);
				if($sub > 0) {
					$period = $sub;
				}
			}
		}elseif(false !== strpos($expr, '/')) {
			$part = explode('/', $expr, 2);
			if(count($part) == 2 && is_numeric($part[1])) {
				$period = $part[1];
			}
		}

		return $period;
    }

}