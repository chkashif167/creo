<?php

class Raveinfosys_Exporter_Model_Operations_Creditmemo extends Mage_Core_Model_Abstract
{
    public function createCreditMemo($order_id,$credit_item,$creditDetail)
	{
	    $order = $this->getOrderModel($order_id);
		try{
		     $data = array('qtys' => $credit_item ,'shipping_amount'=>$creditDetail['refunded_shipping_amount'],
						'adjustment_positive'=>$creditDetail['adjustment_positive'],'adjustment_negative'=>$creditDetail['adjustment_negative']);
		    if(Mage::helper('exporter')->getVersion())
			{
		     $service = Mage::getModel('sales/service_order', $order);
			 $creditMemo = $service->prepareCreditmemo($data); 
			 $creditMemo = $creditMemo->setState(2)->save();
			 $this->updateStatus($order_id,$creditDetail);
			}
			else
			{
			  $creditMemo = Mage::getModel('sales/order_creditmemo_api')
						->create($order_id, $data ,null ,0,0);
			}
			 
			 $model = Mage::getSingleton("sales/order_creditmemo"); 
			 $credit_id = $model->getCollection()->getLastItem()->getId();
			              $model->load($credit_id)
								  ->setCreatedAt($creditDetail['creditmemo_created_at'])
								  ->setUpdatedAt($creditDetail['creditmemo_created_at'])
								  ->save()
								  ->unsetData();
								  
			 $this->updateCreditQTY($credit_item);
			
		}catch (Exception $e) {
		
		 Mage::helper('exporter')->logException($e,$order->getIncrementId(),'creditmemo');
		 Mage::helper('exporter')->footer();
		 return true;
		}
		$order->unsetData();
		return true;
	}
	
	
	
	public function updateCreditQTY($credit_item)
	{
	  foreach($credit_item as $itemid => $itemqty)
	  {
	   $orderItem = Mage::getModel('sales/order_item')->load($itemid);
	   $orderItem->setQtyRefunded($itemqty)->save();
	   $orderItem->unsetData();
	  }
	}
	
	public function updateStatus($order_id,$refunded)
    {
       $order = $this->getOrderModel($order_id);
	   
	   //set creditmemo data
		$order->setSubtotalRefunded($refunded['refunded_subtotal'])	
		       ->setBaseSubtotalRefunded($refunded['refunded_subtotal'])	
		       ->setTaxRefunded($refunded['refunded_tax_amount'])	
		       ->setBaseTaxRefunded($refunded['base_refunded_tax_amount'])	
		       ->setDiscountRefunded($refunded['refunded_discount_amount'])	
		       ->setBaseDiscountRefunded($refunded['base_refunded_discount_amount'])	
		       ->setShippingRefunded($refunded['refunded_shipping_amount'])	
		       ->setBaseShippingRefunded($refunded['base_refunded_shipping_amount'])	
		       ->setTotalRefunded($refunded['total_refunded'])	
		       ->setBaseTotalRefunded($refunded['base_total_refunded'])	
		       ->setTotalOfflineRefunded($refunded['total_refunded'])	
		       ->setBaseTotalOfflineRefunded($refunded['base_total_refunded'])	
		       ->setAdjustmentNegative($refunded['adjustment_positive'])	
		       ->setBaseAdjustmentNegative($refunded['adjustment_positive'])	
		       ->setAdjustmentPositive($refunded['adjustment_negative'])
		       ->setBaseAdjustmentPositive($refunded['adjustment_negative'])
			   ->save();
	   $order->unsetData();
    }
	
	public function getOrderModel($last_order_increment_id)
    {
		$order = Mage::getModel('sales/order')->loadByIncrementId($last_order_increment_id);
		return $order;
    }
}