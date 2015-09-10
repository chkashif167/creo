<?php
class VES_Core_Model_Feed extends Mage_Core_Model_Abstract
{
    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl()
    {
    	$modules = Mage::app()->getConfig()->getModuleConfig()->asArray();
    	$vnecomsExt = array();
    	foreach($modules as $moduleName=>$moduleInfo){
    		if(!isset($moduleInfo['active']) || $moduleInfo['active'] != 'true'){
    			continue;
    		}
    		$nameSpace	= explode('_', $moduleName);
    		if((sizeof($nameSpace) == 2) && ($nameSpace[0] == 'VES')){
    			$vnecomsExt[] = $moduleName.'|'.isset($moduleInfo['version'])?$moduleInfo['version']:'';
    		}
    	}
    	$params = array();
    	$params[] = 'exts='.urlencode(implode('||', $vnecomsExt));
    	$params[] = 'url='.urlencode(Mage::getStoreConfig('web/unsecure/base_url'));
    	$params = implode('&', $params);
    	return 'http://www.vnecoms.com/news/rss/?'.$params;
    }

    /**
     * Check feed for modification
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function checkUpdate()
    {
       	if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
            return $this;
        }

        $feedData = array();

        $feedXml = $this->getFeedData();
        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            foreach ($feedXml->channel->item as $item) {
                $feedData[] = array(
                    'severity'      => (int)$item->severity,
                    'date_added'    => $this->getDate((string)$item->pubDate),
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

        return $this;
    }

    /**
     * Retrieve DB date from RSS date
     *
     * @param string $rssDate
     * @return string YYYY-MM-DD YY:HH:SS
     */
    public function getDate($rssDate)
    {
        return gmdate('Y-m-d H:i:s', strtotime($rssDate));
    }

    /**
     * Retrieve Update Frequency
     *
     * @return int
     */
    public function getFrequency()
    {
        return 172800; /*1 days*/
    }

    /**
     * Retrieve Last update time
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return Mage::app()->loadCache('vnecoms_admin_version_lastcheck');
    }

    /**
     * Set last update time (now)
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'vnecoms_admin_version_lastcheck');
        return $this;
    }
	/**
     * Retrieve feed data as XML element
     *
     * @return SimpleXMLElement
     */
    public function getFeedData()
    {
        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig(array(
            'timeout'   => 2
        ));
        $curl->write(Zend_Http_Client::GET, $this->getFeedUrl(), '1.0');
        $data = $curl->read();
        if ($data === false) {
            return false;
        }
        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);
        $curl->close();

        try {
            $xml  = new SimpleXMLElement($data);
        }
        catch (Exception $e) {
            return false;
        }

        return $xml;
    }

    public function getFeedXml()
    {
        try {
            $data = $this->getFeedData();
            $xml  = new SimpleXMLElement($data);
        }
        catch (Exception $e) {
            $xml  = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?>');
        }

        return $xml;
    }
}
