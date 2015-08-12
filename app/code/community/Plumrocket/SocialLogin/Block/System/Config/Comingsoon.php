<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_SocialLogin
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


class Plumrocket_SocialLogin_Block_System_Config_Comingsoon extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
    	// $moduleNode     = Mage::getConfig()->getNode('modules/Plumrocket_SocialLogin');
     	//    $name           = $moduleNode->name;
     	//    $url 			= 'https://store.plumrocket.com/magento-extensions/social-login-pro-magento-extension.html';

        return '<div style="padding:10px;background-color:#fff;color:#d83820;border:1px solid #ddd;margin-bottom:7px;">'.
			$this->__('This network is coming soon. It will be available via free update.')
		.'</div>';
    }		            
}