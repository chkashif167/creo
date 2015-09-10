<?php
class VES_Core_Block_Notification extends Mage_Core_Block_Template
{
	public function _toHtml(){
		if(!Mage::getSingleton('adminhtml/session')->getData('check_ves_notification_message')){
			Mage::helper('ves_core')->checkExtensions();
			Mage::getSingleton('adminhtml/session')->setData('check_ves_notification_message',1);
		}
		$notifications = Mage::getSingleton('adminhtml/session')->getData('ves_notification_message');
		$html = '';
		foreach($notifications as $notification){
			$html .= '<div class="notification-global">'.$notification.'</div>';
		}
		return $html;
	}
}