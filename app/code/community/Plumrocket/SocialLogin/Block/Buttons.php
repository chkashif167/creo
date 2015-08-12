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


class Plumrocket_SocialLogin_Block_Buttons extends Mage_Core_Block_Template
{
    protected $_loaderImg = 'loader.gif';
    protected $_countFullButtons = 6;

    public function getPreparedButtons($part = null)
    {
        return Mage::helper('pslogin')->getPreparedButtons($part);
    }

    public function hasButtons()
    {
        return (bool)$this->getPreparedButtons();
    }

    public function showLoginFullButtons()
    {
        $visible = $this->getPreparedButtons('visible');
        return count($visible) <= $this->_countFullButtons;
    }

    public function showRegisterFullButtons()
    {
        return $this->showFullButtons();
    }

    public function showFullButtons()
    {
        $all = $this->getPreparedButtons();
        return count($all) <= $this->_countFullButtons;
    }

    public function getLoaderUrl()
    {
        return Mage::getDesign()->getSkinUrl('images/plumrocket/pslogin/'. $this->_loaderImg);
    }

    public function setFullButtonsCount($count)
    {
        if(is_numeric($count) && $count >= 0) {
            $this->_countFullButtons = $count;
        }
        return $this;
    }

}