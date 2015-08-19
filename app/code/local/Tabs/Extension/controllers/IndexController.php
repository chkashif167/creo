<?php
class Tabs_Extension_IndexController extends Mage_Core_Controller_Front_Action{
    
    public function IndexAction() {
      
	 echo "Hello tuts+ World"; 
	  
    }

    public function SellerAction() 
    {
    	
    	$this->loadLayout();
        $this->renderLayout();
    }

    public function NewAction() 
    {
    	
    	$this->loadLayout();
        $this->renderLayout();
    }

    public function TrendingAction() 
    {
    	
    	$this->loadLayout();
        $this->renderLayout();
    }
    
}