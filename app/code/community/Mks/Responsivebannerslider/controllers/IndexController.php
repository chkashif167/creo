<?php
class Mks_Responsivebannerslider_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Responsivebannerslider"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("responsivebannerslider", array(
                "label" => $this->__("Responsivebannerslider"),
                "title" => $this->__("Responsivebannerslider")
		   ));

      $this->renderLayout(); 
	  
    }
}