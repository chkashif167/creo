<?php

class Raveinfosys_Exporter_Model_Operations_Shipment extends Mage_Core_Model_Abstract
{
    
    public function createShipment($order_id,$shipped_item,$date)
	{
	    $order = $this->getOrderModel($order_id);
		try
		  {
		    if($order->canShip()) 
			{          
				$shipId = Mage::getModel('sales/order_shipment_api')
						->create($order_id, $shipped_item ,null ,0,0);
				
				if($shipId)
				{
				  Mage::getSingleton("sales/order_shipment")->loadByIncrementId($shipId)
								  ->setCreatedAt($date)
								  ->setUpdatedAt($date)
								  ->save()
								  ->unsetData();
				  $this->updateShipmentQTY($shipped_item);
				}  
			}
		  } catch (Exception $e) {
		    Mage::helper('exporter')->logException($e,$order->getIncrementId(),'shipment');
			Mage::helper('exporter')->footer();
			return true;
		  }
		  $order->unsetData();
		 return $shipment; 
	}
	
	public function updateShipmentQTY($shipped_item)
	{ 
	  foreach($shipped_item as $itemid => $itemqty)
	  {
	   $orderItem = Mage::getModel('sales/order_item')->load($itemid);
	   $orderItem->setQtyShipped($itemqty)->save();
	   $orderItem->unsetData();
	  } 
	}
	
   public function getOrderModel($last_order_increment_id)
   {
     $order = Mage::getModel('sales/order')->loadByIncrementId($last_order_increment_id);
	 return $order;
   }
}