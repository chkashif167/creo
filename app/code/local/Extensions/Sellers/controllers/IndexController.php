<?php
class Extensions_Sellers_IndexController extends Mage_Core_Controller_Front_Action
{
    public function IndexAction() 
	{
      
    }

    public function BestsellerAction()
    {
        $this->loadLayout();    
        $this->renderLayout();
    }
}