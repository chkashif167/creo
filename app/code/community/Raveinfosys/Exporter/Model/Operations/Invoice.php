<?php

class Raveinfosys_Exporter_Model_Operations_Invoice extends Mage_Core_Model_Abstract
{
    public function createInvoice($order_id,$invoice_item,$date)
	{	
	    $order = $this->getOrderModel($order_id);
		try{
			if($order->canInvoice()) 
			{          
				$invoiceId = Mage::getModel('sales/order_invoice_api')
						->create($order->getIncrementId(), $invoice_item ,null ,0,0);
				
				if($invoiceId)
				{
				   Mage::getSingleton("sales/order_invoice")->loadByIncrementId($invoiceId)
								  ->setCreatedAt($date)
								  ->setUpdatedAt($date)
								  ->save()
								  ->unsetData();
				   $this->updateInvoiceQTY($invoice_item);
				} 
			}
		} catch (Exception $e) {
		 Mage::helper('exporter')->logException($e,$order->getIncrementId(),'invoice');
		 Mage::helper('exporter')->footer();
		 return true;
		}
		$order->unsetData();
		return $invoiceId;
	} 
	
	public function updateInvoiceQTY($invoice_item)
	{ 
	  foreach($invoice_item as $itemid => $itemqty)
	  {
	   $orderItem = Mage::getModel('sales/order_item')->load($itemid);
	   $orderItem->setQtyInvoiced($itemqty)->save();
	   $orderItem->unsetData();
	  } 
	}
	
	public function getOrderModel($last_order_increment_id)
    {
     $order = Mage::getModel('sales/order')->loadByIncrementId($last_order_increment_id);
	 return $order;
    }
}