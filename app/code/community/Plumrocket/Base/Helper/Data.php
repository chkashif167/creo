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
@copyright	Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Base_Helper_Data extends Plumrocket_Base_Helper_Main
{
	public function isAdminNotificationEnabled()
	{
		$m = 'Mage_Admin'.'Not'.'ification';
		return (($module = Mage::getConfig()->getModuleConfig($m))
			&& ($module->is('active', 'true'))
			&& !Mage::getStoreConfig($this->_getAd().'/'.$m));
	}


	public function getAllPlumrocketModules()
	{
		$modules = (array)Mage::getConfig()->getNode('modules')->children();

		$result = array();
		foreach($modules as $key => $module) {
			if ( strpos($key, 'Plumrocket_') !== false && $module->is('active') && !Mage::getStoreConfig($this->_getAd().'/'.$key) ) {
				$result[$key] = $module;
			}
		}

		return $result;
	}

	protected function _getAd()
	{
		return 'adva'.'nced/modu'.
			'les_dis'.'able_out'.'put';
	}

	public function moduleEnabled($store = null)
	{
		return true;
	}


}
