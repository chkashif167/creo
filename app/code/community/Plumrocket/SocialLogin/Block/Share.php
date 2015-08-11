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


class Plumrocket_SocialLogin_Block_Share extends Mage_Core_Block_Template
{
	protected $_share = array(
							'facebook',
							'twitter',
							'google_plusone_share' => 'Google+',
							'linkedin' => 'LinkedIn',
							'pinterest',
							'amazonwishlist' => 'Amazon',
							'vk' => 'Vkontakte',
							'odnoklassniki_ru' => 'Odnoklassniki',
							'mymailru' => 'Mail',
							'blogger',
							'delicious',
							'wordpress',
						);

    public function showPopup()
    {
        return Mage::helper('pslogin')->showPopup() && Mage::helper('pslogin')->shareEnabled();
    }

    public function getButtons()
    {
    	$buttons = array();

    	$url = urlencode($this->getPageUrl());
    	$title = urlencode($this->getTitle());

    	foreach ($this->_share as $key1 => $key2) {
    		$key = (!is_numeric($key1)) ? $key1 : $key2;
    		$name = ucfirst($key2);

    		$buttons[] = array(
                'href' => "https://api.addthis.com/oexchange/0.8/forward/{$key}/offer?url={$url}&ct=1&pco=tbxnj-1.0",
    			// 'href' => "https://api.addthis.com/oexchange/0.8/forward/{$key}/offer?url={$url}&title={$title}&ct=1&pco=tbxnj-1.0",
    			'image' => "https://cache.addthiscdn.com/icons/v2/thumbs/32x32/{$key}.png",
    			'name' => $name,
    		);
    	}

    	return $buttons;
    }

    public function getPageUrl()
    {
    	$pageUrl = null;
    	$shareData = Mage::helper('pslogin')->getShareData();
    	
    	switch($shareData['page']) {

            case '__custom__':
                $pageUrl = $shareData['page_link'];
                if (!Mage::helper('pslogin')->isUrlInternal($pageUrl)) {
                    $pageUrl = Mage::getBaseUrl() . $pageUrl;
                }
                break;

            case '__invitations__':
                if(Mage::helper('pslogin')->moduleInvitationsEnabled()) {
                    $pageUrl = Mage::helper('invitations')->getRefferalLink();
                }else{
                    $pageUrl = Mage::getBaseUrl();
                }
            	break;

            default:
                if(is_numeric($shareData['page'])) {
                    $pageUrl = Mage::helper('cms/page')->getPageUrl($shareData['page']);
                }
        }

        // Disable addsis analytics anchor.
        $pageUrl .= '#';

        return $pageUrl;
    }

    public function getTitle()
    {
    	$shareData = Mage::helper('pslogin')->getShareData();
    	return $shareData['title'];
    }

    public function getDescription()
    {
        $cms = Mage::helper('cms');
        $process = $cms->getBlockTemplateProcessor();
    	
        $shareData = Mage::helper('pslogin')->getShareData();
    	return $process->filter($shareData['description']);
    }

}