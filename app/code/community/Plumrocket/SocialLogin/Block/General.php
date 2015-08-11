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


class Plumrocket_SocialLogin_Block_General extends Mage_Core_Block_Template
{
	protected function _toHtml()
    {
        $helper = Mage::helper('pslogin');
        if(!$helper->moduleEnabled()) {
            return;
        }
        
        $moduleName = $this->getRequest()->getModuleName();

        // Set current store.
        if($moduleName != 'pslogin') {
            $currentStoreId = Mage::app()->getStore()->getId();
            $helper->refererStore($currentStoreId);
        }

        // Set referer.
        if(!$customerId = Mage::getSingleton('customer/session')->getCustomerId()) {
            $skipModules = $helper->getRefererLinkSkipModules();
            if($this->getRequest()->getActionName() != 'noRoute' && !in_array($moduleName, $skipModules)) {
                $referer = $this->helper('core/url')->getCurrentBase64Url();
                $helper->refererLink($referer);
            }
        }
        
        return parent::_toHtml();
    }
}