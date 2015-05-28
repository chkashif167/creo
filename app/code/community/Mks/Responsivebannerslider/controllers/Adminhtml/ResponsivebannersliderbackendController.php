<?php
class Mks_Responsivebannerslider_Adminhtml_ResponsivebannersliderbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("About Us"));
	   $this->renderLayout();
    }
}