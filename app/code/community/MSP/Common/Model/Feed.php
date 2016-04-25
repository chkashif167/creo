<?php
/**
 * IDEALIAGroup srl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@idealiagroup.com so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_Common
 * @copyright  Copyright (c) 2013 IDEALIAGroup srl (http://www.idealiagroup.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class MSP_Common_Model_Feed extends Mage_AdminNotification_Model_Feed
{
	const XML_PATH_MSP_RSS_URL = 'msp_common/rss/url';

	/**
	 * (non-PHPdoc)
	 * @see Mage_AdminNotification_Model_Feed::getFeedUrl()
	 */
	public function getFeedUrl()
	{
		if (is_null($this->_feedUrl))
		{
			$this->_feedUrl = Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://';
			$this->_feedUrl.= Mage::getStoreConfig(self::XML_PATH_MSP_RSS_URL);
		}

		return $this->_feedUrl;
	}

	public function checkUpdate()
	{
		if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
			return $this;
		}

		$feedData = array();

		$feedXml = $this->getFeedData();

		if ($feedXml && $feedXml->channel && $feedXml->channel->item)
		{
			foreach ($feedXml->channel->item as $item)
			{
				$feedData[] = array(
					'severity'      => (int)$item->custom_fields->severity,
					'date_added'    => $this->getDate((string)$item->pubDate),
					'title'         => 'MageSpecialist - '.((string)$item->title),
					'description'   => (string)$item->description,
					'url'           => (string)$item->link,
				);
			}

			if ($feedData) {
				Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
			}

		}
		$this->setLastUpdate();

		return $this;
	}
}