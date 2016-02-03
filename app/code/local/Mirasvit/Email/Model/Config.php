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


class Mirasvit_Email_Model_Config extends Varien_Object
{
	public function getTestEmail()
	{
		$email = Mage::getStoreConfig('email/test/email');
        if (!$email) {
            $email = Mage::getStoreConfig('trans_email/ident_general/email');
        }

        return $email;
	}

	public function validateCron()
	{
		$result = true;

		$cronCollection = Mage::getModel('cron/schedule')->getCollection()
			->addFieldToFilter('executed_at', array('notnull' => true))
			->setOrder('executed_at', 'desc')
			->setPageSize(1);
		if ($cronCollection->count() == 0) {
			$result = Mage::helper('email')->__('For correct extension works, please configure magento cron job.');
		} else {
			$schedule    = $cronCollection->getFirstItem();
			$currentTime = Mage::getSingleton('core/date')->gmtTimestamp();
			$cronTime    = strtotime($schedule->getExecutedAt());

			if ($currentTime > $cronTime + 10 * 60) {
				$result = Mage::helper('email')->__('Last cron execution time is %s GMT. Please check magento cron job.', $schedule->getExecutedAt());
			}
		}

		return $result;
	}

	public function isSandbox()
	{
		return (bool) Mage::getStoreConfig('trigger_email/test/sandbox');
	}

	public function getSandboxEmail()
	{
		return  Mage::getStoreConfig('trigger_email/test/email');
	}

	public function getEmailLimit()
	{
		return (int) Mage::getStoreConfig('trigger_email/general/max_email');
	}

	public function getEmailLimitPeriod()
	{
		return (int) Mage::getStoreConfig('trigger_email/general/max_email_period');
	}
}