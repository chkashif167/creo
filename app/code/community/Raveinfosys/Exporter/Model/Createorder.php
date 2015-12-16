<?php

class Raveinfosys_Exporter_Model_Createorder extends Mage_Core_Model_Abstract
{
    public $last_order_increment_id = null ;
    public $order_items = array() ;
    public $shipped_item = array() ;
    public $invoiced_item = array() ;
    public $credit_item = array() ;
    public $canceled_item = array() ;
	public $partial_shipped = false;
	public $partial_credited = false;
	public $partial_invoiced = false;
	public $order_status = null;
	public $order_detai_arr = null;
    public $parent_id_flag = 0;
    public $parent_id = 0;
	public $store_id = 0;
	public $invoice_created_at = '';
	public $shipment_created_at = '';
	
    public function _construct()
    {
	    $this->setLastOrderItemId();
	}
	
    public function setItemData()
    {
	   $order = $this->getOrderModel($this->last_order_increment_id);
	   $shipped_item = array();
	   $invoiced_item = array();
	   $credit_item = array();
	   $canceled_item = array();
	   $itemCount = 0;
	   $orderItems = $this->order_items;
	   foreach($order->getAllItems() as $item) 
	   {
	     $shipped_item[$item->getItemId()] = $orderItems[$itemCount]['qty_shipped'];
		 $invoiced_item[$item->getItemId()] = $orderItems[$itemCount]['qty_invoiced'];
		 $credit_item[$item->getItemId()] = $orderItems[$itemCount]['qty_refunded'];
		 $canceled_item[$item->getItemId()] = $orderItems[$itemCount]['qty_canceled'];
		 
		 if($orderItems[$itemCount]['qty_shipped']>0)
		 $this->partial_shipped = true;
		 
		 if($orderItems[$itemCount]['qty_invoiced']>0)
		 $this->partial_invoiced = true;
		 
		 if($orderItems[$itemCount]['qty_refunded']>0)
		 $this->partial_credited = true;
		 
		 $itemCount++;
	   }
	   
	   $this->invoiced_item = $invoiced_item;
	   $this->shipped_item = $shipped_item;
	   $this->credit_item = $credit_item;
	   $this->canceled_item = $canceled_item;
	}
	
	public function setGlobalData($last_order_increment_id,$order_items,$sales_order_arr)
	{
	  $this->last_order_increment_id = $last_order_increment_id;
	  $this->order_items = $order_items;
	  $this->order_detai_arr = $sales_order_arr;
	  $this->order_status = $sales_order_arr['order_state'];
	  $this->invoice_created_at = $sales_order_arr['invoice_created_at'];
	  $this->shipment_created_at = $sales_order_arr['shipment_created_at'];
	  $this->setTime($last_order_increment_id,$sales_order_arr);
	  $this->setItemData();
	}
	
	public function setTime($last_order_increment_id,$sales_order_arr)
	{
	  Mage::getModel('sales/order')->loadByIncrementId($last_order_increment_id)
										->setCreatedAt($sales_order_arr['created_at'])
										->setUpdatedAt($sales_order_arr['updated_at'])
										->save()
										->unsetData();
	}
	
	public function setLastOrderItemId()
	{
	   $resource = Mage::getSingleton('core/resource');
	   $conn = $resource->getConnection('core_read');
	   $results = $conn->query("SHOW TABLE STATUS LIKE '".$resource->getTableName('sales/order_item')."'")->fetchAll();
	   foreach($results as $data)
	   $this->parent_id_flag = $data['Auto_increment']-1;	
	}
	
	
	 //To create order
    public function createOrder($sales_order_arr,$sales_order_item_arr,$store_id)
    {
	     $this->store_id = $store_id;
		 if(!$this->orderIdStatus($sales_order_arr['increment_id']))
		 return 2;  
		 
		 $transaction = Mage::getSingleton('core/resource_transaction');
		 $order = Mage::getModel('sales/order')
		  ->setIncrementId($sales_order_arr['increment_id'])
		  ->setStoreId($this->store_id)
		  ->setStatus($sales_order_arr['order_status'])
		  ->setHoldBeforeState($sales_order_arr['hold_before_state'])
		  ->setHoldBeforeStatus($sales_order_arr['hold_before_status'])
		  ->setIsVirtual($sales_order_arr['is_virtual'])
		  ->setBaseCurrencyCode($sales_order_arr['base_currency_code'])
		  ->setStoreCurrencyCode($sales_order_arr['store_currency_code'])
		  ->setGlobalCurrencyCode($sales_order_arr['store_currency_code'])
		  ->setOrderCurrencyCode($sales_order_arr['order_currency_code']);
		  
		  // Set Customer data
		  
		 $custm_detail = $this->getCustomerInfo($sales_order_arr['customer_email']);
		 
		 if($custm_detail)
		 {
		   $order->setCustomerEmail($custm_detail['email'])
		    ->setCustomerFirstname($custm_detail['firstname'])
		    ->setCustomerLastname($custm_detail['lastname'])
		    ->setCustomerId($custm_detail['entity_id'])
		    ->setCustomerGroupId($custm_detail['group_id']);
		 }
		 else
		 {
		    $order->setCustomerEmail($sales_order_arr['customer_email'])
		    ->setCustomerFirstname($sales_order_arr['customer_firstname'])
		    ->setCustomerLastname($sales_order_arr['customer_lasttname'])
			->setCustomerIsGuest(1)
		    ->setCustomerGroupId(0);
		 }	
		  
	
		  // Set Billing Address
		 $billingAddress = Mage::getModel('sales/order_address')
		  ->setStoreId($this->store_id)
		  ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
		  ->setCustomerAddressId($sales_order_arr['billing_address']['customer_address_id'])
		  ->setPrefix($sales_order_arr['billing_address']['prefix'])
		  ->setFirstname($sales_order_arr['billing_address']['firstname'])
		  ->setMiddlename($sales_order_arr['billing_address']['middlename'])
		  ->setLastname($sales_order_arr['billing_address']['lastname'])
		  ->setSuffix($sales_order_arr['billing_address']['suffix'])
		  ->setCompany($sales_order_arr['billing_address']['company'])
		  ->setStreet($sales_order_arr['billing_address']['street'])
		  ->setCity($sales_order_arr['billing_address']['city'])
		  ->setCountryId($sales_order_arr['billing_address']['country_id'])
		  ->setRegion($sales_order_arr['billing_address']['region'])
		  ->setPostcode($sales_order_arr['billing_address']['postcode'])
		  ->setTelephone($sales_order_arr['billing_address']['telephone'])
		  ->setFax($sales_order_arr['billing_address']['fax']);
		 $order->setBillingAddress($billingAddress);
		
		 // Set Shipping Address
		 $shippingAddress = Mage::getModel('sales/order_address')
		  ->setStoreId($this->store_id)
		  ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
		  ->setCustomerAddressId($sales_order_arr['shipping_address']['customer_address_id'])
		  ->setPrefix($sales_order_arr['shipping_address']['prefix'])
		  ->setFirstname($sales_order_arr['shipping_address']['firstname'])
		  ->setMiddlename($sales_order_arr['shipping_address']['middlename'])
		  ->setLastname($sales_order_arr['shipping_address']['lastname'])
		  ->setSuffix($sales_order_arr['shipping_address']['suffix'])
		  ->setCompany($sales_order_arr['shipping_address']['company'])
		  ->setStreet($sales_order_arr['shipping_address']['street'])
		  ->setCity($sales_order_arr['shipping_address']['city'])
		  ->setCountry_id($sales_order_arr['shipping_address']['country_id'])
		  ->setRegion($sales_order_arr['shipping_address']['region'])
		  ->setPostcode($sales_order_arr['shipping_address']['postcode'])
		  ->setTelephone($sales_order_arr['shipping_address']['telephone'])
		  ->setFax($sales_order_arr['shipping_address']['fax']) ;
		
		 if(!$sales_order_arr['is_virtual']){
		   $order->setShippingAddress($shippingAddress)
		   ->setShippingMethod($sales_order_arr['shipping_method'])
		   ->setShippingDescription($sales_order_arr['shipping_method']);
		 }   
		
		 $orderPayment = Mage::getModel('sales/order_payment')
		  ->setStoreId($this->store_id)
		  ->setCustomerPaymentId(0)
		  ->setMethod('checkmo')
		  ->setPoNumber(' - ');
		 $order->setPayment($orderPayment);
		
		 $flag = 1;
		 foreach($sales_order_item_arr as $product)
		 {
		   $orderItem = Mage::getModel('sales/order_item')
			->setStoreId($this->store_id)
			->setQuoteItemId(0)
			->setQuoteParentItemId(NULL)
			->setSku($product['product_sku']) 
			->setProductType($product['product_type'])
			->setProductOptions(unserialize($product['product_option']))
			->setQtyBackordered(NULL)
			->setTotalQtyOrdered($product['qty_ordered'])
			->setQtyOrdered($product['qty_ordered'])
			->setName($product['product_name'])
			->setPrice($product['original_price'])
			->setBasePrice($product['base_original_price'])
			->setOriginalPrice($product['original_price'])
			->setBaseOriginalPrice($product['base_original_price'])
			->setRowWeight($product['row_weight'])
			->setPriceInclTax($product['price_incl_tax'])
			->setBasePriceInclTax($product['base_price_incl_tax'])
			->setTaxAmount($product['product_tax_amount'])
			->setBaseTaxAmount($product['product_base_tax_amount'])
			->setTaxPercent($product['product_tax_percent'])
			->setDiscountAmount($product['product_discount'])
			->setBaseDiscountAmount($product['product_base_discount'])
			->setDiscountPercent($product['product_discount_percent'])
			->setRowTotal($product['row_total'])
			->setBaseRowTotal($product['base_row_total']);
			
			if($product['is_child']=='yes')
			$orderItem->setParentItemId($this->parent_id);
			
			else if($product['is_child']=='no')
			$this->parent_id = $this->parent_id_flag+$flag;
			
			$order->addItem($orderItem);
			
			$flag++;
		  }
		
		
		 $order->setShippingAmount($sales_order_arr['shipping_amount']);
		 $order->setBaseShippingAmount($sales_order_arr['base_shipping_amount']);

		  //Apply Discount
		 $order->setBaseDiscountAmount($sales_order_arr['base_discount_amount']);
		 $order->setDiscountAmount($sales_order_arr['discount_amount']);
		
		  //Apply Tax
		 $order->setBaseTaxAmount($sales_order_arr['base_tax_amount']);
		 $order->setTaxAmount($sales_order_arr['tax_amount']);

		 $order->setSubtotal($sales_order_arr['subtotal'])      
		  ->setBaseSubtotal($sales_order_arr['base_subtotal'])  
		  ->setGrandTotal($sales_order_arr['grand_total'])      
		  ->setBaseGrandTotal($sales_order_arr['base_grand_total'])
		  ->setShippingTaxAmount($sales_order_arr['shipping_tax_amount'])      
		  ->setBaseShippingTaxAmount($sales_order_arr['base_shipping_tax_amount'])      
		  ->setBaseToGlobalRate($sales_order_arr['base_to_global_rate'])      
		  ->setBaseToOrderRate($sales_order_arr['base_to_order_rate'])      
		  ->setStoreToBaseRate($sales_order_arr['store_to_base_rate'])      
		  ->setStoreToOrderRate($sales_order_arr['store_to_order_rate'])      
		  ->setSubtotalInclTax($sales_order_arr['subtotal_incl_tax'])      
		  ->setBaseSubtotalInclTax($sales_order_arr['base_subtotal_incl_tax'])      
		  ->setCouponCode($sales_order_arr['coupon_code']) 
		  ->setDiscountDescription($sales_order_arr['coupon_code']) 
		  ->setShippingInclTax($sales_order_arr['shipping_incl_tax']) 
		  ->setBaseShippingInclTax($sales_order_arr['base_shipping_incl_tax']) 
		  ->setTotalQtyOrdered($sales_order_arr['total_qty_ordered'])
		  ->setRemoteIp($sales_order_arr['remote_ip']);
		
		 $transaction->addObject($order);
		 $transaction->addCommitCallback(array($order, 'place'));
		 $transaction->addCommitCallback(array($order, 'save'));
		
		 if($transaction->save())
		 {
		  $this->setLastOrderItemId(); 
		  $last_order_increment_id = Mage::getSingleton("sales/order")->getCollection()->getLastItem()->getIncrementId();
		 
		  $this->setGlobalData($last_order_increment_id,$sales_order_item_arr,$sales_order_arr);
		 
		  if($sales_order_arr['order_state']=='processing' || $sales_order_arr['order_state']=='complete')
		  return $this->setProcessing();
		 
		  if($sales_order_arr['order_state']=='canceled')
		  return $this->setCanceled();
		  
		  if($sales_order_arr['order_state']=='closed')
		  return $this->setClosed();
		 
		  if($sales_order_arr['order_state']=='holded')
		  return $this->setHolded();
		 
		  if($sales_order_arr['order_state']=='payment_review')
		  return $this->setPaymentReview();
		 
		  return 1;
		 } 
		 else
		 return 3;
     
    }
	
	
	public function setProcessing()
	{
	   if($this->partial_invoiced)
	   $resp = $this->getInvoiceObj()->createInvoice($this->last_order_increment_id,$this->invoiced_item,$this->invoice_created_at);
	   
	   if($this->partial_shipped)
	   $resp = $this->getShipmentObj()->createShipment($this->last_order_increment_id,$this->shipped_item,$this->shipment_created_at);
	   
	   if($this->partial_credited)
	   $resp = $this->getCreditmemoObj()->createCreditMemo($this->last_order_increment_id,$this->credit_item,$this->order_detai_arr);
	   
	   $this->unsetAllData();
	   return 1;
	}
	
	public function setHolded()
	{
	  try
	  {
	    if($this->setProcessing()== 1)
		{
		 $order = $this->getOrderModel($this->last_order_increment_id);
	     $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true)->save();
		 $order->unsetData();
		 return 1;
		} 
	  } catch (Exception $e) {
	     Mage::helper('exporter')->logException($e,$order->getIncrementId(),'order');
		 Mage::helper('exporter')->footer();return 1;}
	}
	
	public function setPaymentReview()
	{
	  try
	  {
	    if($this->setProcessing()== 1)
		{
		 $order = $this->getOrderModel($this->last_order_increment_id);
	     $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true)->save();
		 $order->unsetData();
		 return 1;
		} 
	  } catch (Exception $e) {
	     Mage::helper('exporter')->logException($e,$order->getIncrementId(),'order');
		 Mage::helper('exporter')->footer();return 1;}
	}
	
	
	public function setCanceled()
	{
	  try
	  {
	    if($this->setProcessing()== 1)
		{
		  $this->updateCanceledQTY();
		  $order = $this->getOrderModel($this->last_order_increment_id);
	      $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
		  $order->unsetData();
		  return 1;
		} 
	  } catch (Exception $e) {
	     Mage::helper('exporter')->logException($e,$order->getIncrementId(),'order');
		 Mage::helper('exporter')->footer();return 1;}
	}
	
	public function setClosed()
	{
	  try
	  {
	    if($this->setProcessing()== 1)
		{
		  $order = $this->getOrderModel($this->last_order_increment_id);
	      $order->setStatus(Mage_Sales_Model_Order::STATE_CLOSED, true)->save();
		  $order->unsetData();
		  return 1;
		} 
	  } catch (Exception $e) {
	     Mage::helper('exporter')->logException($e,$order->getIncrementId(),'order');
		 Mage::helper('exporter')->footer();return 1;}
	}
	
	
	public function updateCanceledQTY()
	{ 
	  $items = $this->canceled_item;
	  foreach($items as $itemid => $itemqty)
	  {
	   $orderItem = Mage::getModel('sales/order_item')->load($itemid);
	   $orderItem->setQtyCanceled($itemqty)->save();
	   $orderItem->unsetData();
	  } 
	}	
	
   public function getOrderModel($last_order_increment_id)
   {
     $order = Mage::getModel('sales/order')->loadByIncrementId($last_order_increment_id);
	 return $order;
   }
   
   public function orderIdStatus($last_order_increment_id)
   {
     $order = Mage::getModel('sales/order')->loadByIncrementId($last_order_increment_id);
	 
	 if($order->getId())
	 return false;
	 else
	 return true;
   }
   
   public function unsetAllData()
   {
     $this->shipped_item = array() ;
     $this->invoiced_item = array() ;
     $this->credit_item = array() ;
     $this->canceled_item = array() ;
	 $this->partial_shipped = false;
	 $this->partial_credited = false;
	 $this->partial_invoiced = false;
	 $this->order_detai_arr = false;
   }
   
   public function getInvoiceObj()
   {
     return Mage::getModel('exporter/operations_invoice');
   }
   
   public function getShipmentObj()
   {
     return Mage::getModel('exporter/operations_shipment');
   }
   
   public function getCreditmemoObj()
   {
     return Mage::getModel('exporter/operations_creditmemo');
   }
   
   public function getCustomerInfo($email)
   {
      $customer = Mage::getModel("customer/customer");
	  $customer->setWebsiteId(Mage::getModel('core/store')->load($this->store_id)->getWebsiteId());
	  if($customer->loadByEmail($email))
	  return $customer->getData();
	  else
	  return false;
   }
   
   public function removeOrderStatusHistory()
   {
     $coll = Mage::getModel('sales/order_status_history')->getCollection()->addFieldToFilter('parent_id',Mage::getSingleton("sales/order")->getCollection()->getLastItem()->getId());
	 foreach($coll as $history)
	 Mage::getModel('sales/order_status_history')->load($history->getId())->delete()->save()->unsetData();
   }
}