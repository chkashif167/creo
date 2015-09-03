<?php

class Magestore_Fblogin_Helper_Data extends Mage_Core_Helper_Abstract{
	public function getAppId(){
		return Mage::getStoreConfig('fblogin/general/app_id');
	}
	
	public function getFbloginUrl(){
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->_getUrl('fblogin/index/index', array('_secure'=>$isSecure));  
	}
	
	public function getSecretId(){
		return Mage::getStoreConfig('fblogin/general/app_secret');
	}
	
	public function createFacebook(){
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'Facebook'.DS.'facebook.php';
		}catch(Exception $e){}
		
		
		// Create our Application instance.
		$facebook = new Facebook(array(
			'appId'  => $this->getAppId(),
			'secret' => $this->getSecretId(),
			'cookie' => true,
		));
		
		return $facebook;
	}
	
	public function getFbUser(){
		$facebook = $this->createFacebook();
    	$userId = $facebook->getUser();
		$fbme = NULL;
		if ($userId) {
			try {
				$fbme = $facebook->api('/me?fields=email,first_name,last_name');
			} catch (FacebookApiException $e) {}
		}
		
		return $fbme;	
	}
}