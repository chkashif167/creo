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


class Plumrocket_SocialLogin_Block_Page_Html_Header extends Mage_Page_Block_Html_Header
{
    
    public function getWelcome()
    {
    	$this->setTemplate('pslogin/page/html/welcome.phtml');
        return Mage_Page_Block_Html_Header::_toHtml();
    }

    public function getMessage()
    {
        return parent::getWelcome();
    }

    public function photoEnabled()
    {
        return Mage::helper('pslogin')->photoEnabled();
    }
    
    public function getPhotoPath()
    {
        return Mage::helper('pslogin')->getPhotoPath(false);
    }

}