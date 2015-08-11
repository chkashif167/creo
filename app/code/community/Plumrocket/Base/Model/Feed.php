<?php 

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

@package	Plumrocket_Base-v1.x.x
@copyright	Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/ 

class Plumrocket_Base_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    const XML_USE_HTTPS_PATH    = 'plumbase/notifications/use_https';
    const XML_FEED_URL_PATH     = 'plumbase/notifications/feed_url';
    const XML_FREQUENCY_PATH    = 'plumbase/notifications/frequency';
    
    protected $_feedUrl;

    public function getFeedUrl()
    {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://')
                . Mage::getStoreConfig(self::XML_FEED_URL_PATH);
        }

        $urlInfo 	= parse_url(Mage::getBaseUrl());
		$domain 	= isset($urlInfo['host']) ? $urlInfo['host'] : '';

		if (!strpos($this->_feedUrl, '/index/')){
			$this->_feedUrl .= 'index/';
		}

		$url = $this->_feedUrl . 'domain/' . urlencode($domain);

		$modulesParams = array();
		$plumrocketModules = Mage::helper('plumbase')->getAllPlumrocketModules();
		foreach($plumrocketModules as $key => $module) {
			$key = str_replace('Plumrocket_', '', $key);
			$modulesParams[] = $key . ( ($module->version) ? ','.$module->version : '' );
		}

		if (count($modulesParams)) {
			$url .= '/modules/'.base64_encode(implode(';', $modulesParams));
		}

        return $url;
    }

    public function checkUpdate()
    {
		if (! Mage::getSingleton('admin/session')->isLoggedIn()
			|| (($this->getFrequency() + $this->getLastUpdate()) > time())
			|| !Mage::helper('plumbase')->isAdminNotificationEnabled()
		) {
			return $this;
		}
		
		try {
			$feedData = array();
			$feedXml = $this->getFeedData();
			
			if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
				foreach ($feedXml->channel->item as $item) {
					$feedData[] = array(
						'severity'		=> (int)$item->severity,
						'date_added'	=> $this->getDate( (string)$item->pubDate ),
						'title'			=> (string)$item->title,
						'description'	=> (string)$item->description,
						'url'			=> (string)$item->link,
					);
				}
			}

			if ($feedData) {
				Mage::getModel('adminnotification/inbox')->parse($feedData);
			}
			$this->setLastUpdate();
			return $this;
		} catch (Exception $e) {
			return false;
		}
    }
    
    public function getLastUpdate()
    {
        return Mage::app()->loadCache('plumrocket_rss_admin_notifications_lastcheck');
    }

    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'plumrocket_rss_admin_notifications_lastcheck');
        return $this;
    }
    
    public function getFrequency()
    {
        return Mage::getStoreConfig(self::XML_FREQUENCY_PATH) * 3600;
    }
}
