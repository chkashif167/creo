<?php   
class Mks_Responsivebannerslider_Block_Index extends Mage_Core_Block_Template{   




public function getResponsivebannersliderEnabled()
    {
        return Mage::getStoreConfig('generalsetting/mksresponsivegroup/enable',Mage::app()->getStore());
    }

public function getResponsivebannersliderSpeed()
    {
        return Mage::getStoreConfig('generalsetting/mksresponsivegroup/slidespeed',Mage::app()->getStore());
    }

public function getResponsivebannerSlideType()
    {
        return Mage::getStoreConfig('generalsetting/mksresponsivegroup/styletype',Mage::app()->getStore());
    }

public function getResponsivebannerBannerLoop()
    {
        return Mage::getStoreConfig('generalsetting/mksresponsivegroup/bannerloop',Mage::app()->getStore());
    }

public function getResponsivebannerPauseOnhover()
    {
        return Mage::getStoreConfig('generalsetting/mksresponsivegroup/pauseonhover',Mage::app()->getStore());
    }


}
