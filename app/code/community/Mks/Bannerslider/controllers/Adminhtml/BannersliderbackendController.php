<?php
class Mks_Bannerslider_Adminhtml_BannersliderbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("About us"));
	   $this->renderLayout();
    }
}