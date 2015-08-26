<?php
class Magebuzz_Info_Model_Feed extends Mage_AdminNotification_Model_Feed {
	const URL_NEWS        = 'http://www.magebuzz.com/feed_news.xml';
	
	public function _construct() {
		parent::_construct();
		$this->_init('info/feed');
	}
	
	public function checkUpdate() {			
		if (!extension_loaded('curl')) {
			return $this;
		}		
		if (!Mage::getStoreConfig('info/notification/enable')) {
			return $this;
		}
		$feedData   = array();
		$feedXml = $this->getFeedData();
		
		if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
			foreach ($feedXml->channel->item as $item) {
				$date = $this->getDate((string)$item->pubDate);
				$feedData[] = array(
					'severity'      => 3,
					'date_added'    => $this->getDate($date),
					'title'         => (string)$item->title,
					'description'   => (string)$item->description,
					'url'           => (string)$item->link,
				);
			}			
			if ($feedData) {
				Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
			}
		}
		$this->setLastUpdate();		
	}
	
	public function getFeedUrl() {
			if (is_null($this->_feedUrl)) {
				$this->_feedUrl = self::URL_NEWS;
			}
			//$query = '?s=' . urlencode(Mage::getStoreConfig('web/unsecure/base_url')); 
			return $this->_feedUrl;
	}
}