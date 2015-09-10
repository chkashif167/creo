<?php

class VES_Core_Model_Observer
{
	
	
	public function controller_action_predispatch(Varien_Event_Observer $observer){
		if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $feedModel  = Mage::getModel('ves_core/feed');
            $feedModel->checkUpdate();
        }
	}
}