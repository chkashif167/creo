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


class Plumrocket_SocialLogin_Block_System_Config_Version extends Plumrocket_Base_Block_System_Config_Version
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_includeJs() . parent::render($element);
    }

    protected function _includeJs()
    {
        $baseJsUrl      = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS);
        $baseSkinUrl    = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
        
        return '<script type="text/javascript">
        var basePopupPath = "' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . '";
        var basePopupSkinPath = "' . $baseSkinUrl . '";
        var wysiwygEditorPath = "' . Mage::getUrl('adminhtml/catalog_category/wysiwyg') . '";
        </script>';
    }
}