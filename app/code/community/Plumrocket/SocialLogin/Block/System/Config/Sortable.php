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


class Plumrocket_SocialLogin_Block_System_Config_Sortable extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    
    public function _construct() {
        parent::_construct();
        $this->setTemplate('pslogin/system/config/sortable.phtml');
        return $this;
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        // $this->assign('element', $element);
        $this->element = $element;
        return $this->toHtml();
    }

    public function getButtons()
    {
        return Mage::helper('pslogin')->getButtons();
    }

    public function getPreparedButtons($part)
    {
        return Mage::helper('pslogin')->getPreparedButtons($part);
    }

}