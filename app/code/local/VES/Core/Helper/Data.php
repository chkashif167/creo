<?php

class VES_Core_Helper_Data extends VES_Core_Helper_Core
{
	/**
     * Retrieve Update Frequency
     *
     * @return int
     */
    public function getFrequency()
    {
        return 2592000; /*30 days*/
    }

    /**
     * Retrieve Last update time
     *
     * @return int
     */
    public function getLastUpdate()
    {
    	$lastUpdate = Mage::app()->loadCache('ves_license_extension_list_lastcheck');
    	if(!$lastUpdate) return $this->setLastUpdate();
        return $lastUpdate;
    }

    /**
     * Set last update time (now)
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function setLastUpdate()
    {
    	$time = time();
        Mage::app()->saveCache($time, 'ves_license_extension_list_lastcheck');
        return $time;
    }
}