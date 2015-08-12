<?php
class Magestore_Fblogin_Block_Fblogin extends Mage_Core_Block_Template
{
	 public function getFbloginUrl(){
	 	$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->getUrl('fblogin/index/index', array('_secure'=>$isSecure));   
	}
	
	public function getFbLoginButtonUrl(){
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		$authUrl = $this->getUrl('fblogin/index/index', array('_secure'=>$isSecure, 'auth'=>1));
		
		$facebook = Mage::helper('fblogin')->createFacebook();
		$loginUrl = $facebook->getLoginUrl(
			array(
				'display'   => 'popup',
				'redirect_uri'      => $authUrl,
				'scope' => 'email',
			)
  		);
		return $loginUrl;
	}
	
	public function getStoreName(){
		return Mage::app()->getStore()->getName();	
	}
	
	public function getAppId(){
		return Mage::getStoreConfig('fblogin/general/app_id');
	}
	
	public function getNotConnectedTemplate(){
		$template = Mage::getStoreConfig('fblogin/general/not_connected_template');
		return str_replace('{{store}}', $this->getStoreName(), $template);
	}
	
	public function getConnectedTemplate(){
		$user = Mage::helper('fblogin')->getFbUser();
		$template = Mage::getStoreConfig('fblogin/general/connected_template');
		$template = str_replace('{{store}}', $this->getStoreName(), $template);
		
		$name = '<a href="'.$user['link'].'">'.$user['name'].'</a>';
		return str_replace('{{user}}', $name, $template);
	}
	
	public function isShowAvatar(){
		return Mage::getStoreConfig('fblogin/general/is_show_avatar');
	}
	
	protected function _beforeToHtml()
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Fblogin')){
			$this->setTemplate(null);
		}
		return parent::_beforeToHtml();
	}		
}