<?php 

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

@package    Plumrocket_Base-v1.x.x
@copyright  Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
@license    http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/


class Plumrocket_Base_Block_System_Config_Form_Field extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $product = Mage::getModel('plumbase/product')->loadByPref(str_replace('_general_serial', '',  $element->getHtmlId()));
        if ($product->isInStock()) {
            $ise = Mage::getConfig()->getModuleConfig('Ent'.'er'.'prise_Checkout') && Mage::getConfig()->getModuleConfig('Ent'.'er'.'prise_Checkout');
            $oldDesign = (version_compare('1.7.0', Mage::getVersion()) >= 0 && !$ise) || (version_compare('1.12.2', Mage::getVersion()) >= 0 && $ise);

            $src = 'images/success_msg_icon.gif';
            $title = implode('', array_map('ch'.'r', explode('.','84.104.97.110.107.32.121.111.117.33.32.89.111.117.114.32.115.101.114.105.97.108.32.107.101.121.32.105.115.32.97.99.99.101.112.116.101.100.46.32.89.111.117.32.99.97.110.32.115.116.97.114.116.32.117.115.105.110.103.32.101.120.116.101.110.115.105.111.110.46')));
            $html = '<div class="field-tooltip" style="background: url('.$this->getSkinUrl($src).') no-repeat 0 0; display: inline-block;width: 15px;height: 15px;position: relative;z-index: 1;vertical-align: middle;"><div '.( $oldDesign ? 'style="display:none;"' : '' ).'>'.$title.'</div></div>';
        } else {
            $html = '<img src="'.$this->getSkinUrl('images/error_msg_icon.gif').'" style="margin-top: 2px;float: right;" />';
        }

        return '<div style="width:300px">'.$element->getElementHtml() . $html.'</div>';
    }

}
