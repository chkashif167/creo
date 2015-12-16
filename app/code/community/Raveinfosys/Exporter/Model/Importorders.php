<?php

class Raveinfosys_Exporter_Model_Importorders extends Mage_Core_Model_Abstract
{
    public $order_info = array();
    public $order_item_info = array();
    public $order_item_flag = 0;
    public $store_id = 0;
    public $import_limit = 0;
    
	
	public function readCSV($csvFile,$data)
    {
		$this->import_limit = $data['import_limit'];
	    $this->store_id = $data['store_id'];
		$file_handle = fopen($csvFile, 'r');
		$i=0;
		$decline = array();
		$available = array();
		$success = 0;
		$parent_flag = 0;
		$invalid = 0;
		$line_number = 2;
		$total_order = 0;
		Mage::helper('exporter')->unlinkFile();
		Mage::helper('exporter')->header();
		while (!feof($file_handle) ) 
		{
			$line_of_text[] = fgetcsv($file_handle);
			
			if($i!=0)
			{
			 if($line_of_text[$i][0]!='' && $parent_flag==0)
			 { 
			   $this->insertOrderData($line_of_text[$i]); 
			   $parent_flag = 1;
			   $total_order++;
			 }
			 else if($line_of_text[$i][91]!='' && $parent_flag == 1 && $line_of_text[$i][0]=='')
			 {
			   $this->insertOrderItem($line_of_text[$i]);
			 }
			 else if($parent_flag==1)
			 {
			   try
			   {
			    $message = Mage::getModel('exporter/createorder')->createOrder($this->order_info,$this->order_item_info,$this->store_id);
			    Mage::getModel('exporter/createorder')->removeOrderStatusHistory();
			   } catch (Exception $e) {
			      Mage::helper('exporter')->logException($e,$this->order_info['increment_id'],'order',$line_number);
				  Mage::helper('exporter')->footer();
				  $decline[] = $this->order_info['increment_id'];
				  $message = 0;
				}
			  
				if($message== 1)
			    $success++;
				
				if($message== 2){
				  Mage::helper('exporter')->logAvailable($this->order_info['increment_id'],'order',$line_number);
				  Mage::helper('exporter')->footer();
				  $decline[] = $this->order_info['increment_id'];
				} 
				
				$this->order_info = array();
			    $this->order_item_info = array();
			    $this->order_item_flag = 0;
				
			    $this->insertOrderData($line_of_text[$i]); 
			    $parent_flag = 1; 
				$line_number = $i+1;
				$total_order++;
			 }
			 
			}
			
			$i++;
			
			if($this->import_limit < $total_order)
			break;
		}
		$isPrintable = Mage::helper('exporter')->isPrintable();
		if($success)
		Mage::getModel('core/session')->addSuccess(Mage::helper('exporter')->__('Total '.$success.' order(s) imported successfully!'));
		  
		if($decline || $isPrintable)
		Mage::getModel('core/session')->addError(Mage::helper('exporter')->__('Click <a href="'.Mage::helper("adminhtml")->getUrl("exporter/adminhtml_exporter/exportLog").'">here</a> to view the error log'));
		
		fclose($file_handle);
		
		return array($success,$decline);
    }
   
   public function insertOrderData($orders_data)
   {
	 $sales_order_arr = array();
	 $sales_order_item_arr = array();
	 $sales_order = $this->getSalesTable();
	 $sales_payment = $this->getSalesPayment();
	 $sales_shipping = $this->getSalesBilling();
	 $sales_billing = $this->getSalesBilling();
	 $sales_order_item = $this->getSalesItem();
	 $model = Mage::getModel('sales/order');		
	 $i = 0;
	 $j = 0;
	 $k = 0;
	 $l = 0;
	 $m = 0;
     foreach($orders_data as $order)
	 {
	   if(count($sales_order)>$i)
	   $sales_order_arr[$sales_order[$i]]= $order;
	   
	   else if(count($sales_billing)>$j)
	   {
	      $sales_billing[$j].$sales_order_arr['billing_address'][$sales_billing[$j]]= $order;
		  $j++;
	   }
	   else if(count($sales_shipping)>$k)
	   {
	      $sales_order_arr['shipping_address'][$sales_shipping[$k]]= $order;
		  $k++;
	   }
	   else if(count($sales_payment)>$l)
	   {
	      $sales_order_arr['payment'][$sales_payment[$l]]= $order;
		  $l++;
	   } 
	   else if(count($sales_order_item)>$m)
	   {
	      $sales_order_item_arr[$sales_order_item[$m]]= $order;
		  $m++;
	   }
	   $i++;
	 }
	 
	 $this->order_info = $sales_order_arr;
	 $this->order_item_info[$this->order_item_flag] = $sales_order_item_arr;
	 $this->order_item_flag++;
   }
   
   public function insertOrderItem($orders_data)
   {
     $sales_order_item_arr = array();
	 $sales_order_item = $this->getSalesItem();
	 $i=0;
	 for($j=91;$j<count($orders_data); $j++)
	 {
	   if(count($sales_order_item)>$i)
	   $sales_order_item_arr[$sales_order_item[$i]]= $orders_data[$j];
	   $i++;
	 }
	 $this->order_item_info[$this->order_item_flag] = $sales_order_item_arr;
	 $this->order_item_flag++;	
   }
   
  
   public function getSalesTable()
   {
     return array(
	 'increment_id',
	 'customer_email',
	 'customer_firstname',
	 'customer_lasttname',
	 'customer_prefix',
	 'customer_middlename',
	 'customer_suffix',
	 'taxvat',
	 'created_at',
	 'updated_at',
	 'invoice_created_at',
	 'shipment_created_at',
	 'creditmemo_created_at',
	 'tax_amount',
	 'base_tax_amount',
	 'discount_amount',
	 'base_discount_amount',
	 'shipping_tax_amount',
	 'base_shipping_tax_amount',
	 'base_to_global_rate',
	 'base_to_order_rate',
	 'store_to_base_rate',
	 'store_to_order_rate',
	 'subtotal_incl_tax',
	 'base_subtotal_incl_tax',
	 'coupon_code',
	 'shipping_incl_tax',
	 'base_shipping_incl_tax',
	 'shipping_method',
	 'shipping_amount',
	 'subtotal',
	 'base_subtotal',
	 'grand_total',
	 'base_grand_total',
	 'base_shipping_amount',
	 'adjustment_positive',
	 'adjustment_negative',
	 'refunded_shipping_amount',
	 'base_refunded_shipping_amount',
	 'refunded_subtotal',
	 'base_refunded_subtotal',
	 'refunded_tax_amount',
	 'base_refunded_tax_amount',
	 'refunded_discount_amount',
	 'base_refunded_discount_amount',
	 'store_id',
	 'order_status',
	 'order_state',
	 'hold_before_state',
	 'hold_before_status',
	 'store_currency_code',
	 'base_currency_code',
	 'order_currency_code',
	 'total_paid',
	 'base_total_paid',
	 'is_virtual',
	 'total_qty_ordered',
	 'remote_ip',
	 'total_refunded',
	 'base_total_refunded',
	 'total_canceled',
	 'total_invoiced');
   }
   
   public function getSalesBilling()
   {
     return array(
	    'customer_address_id',
		'prefix',
		'firstname',
		'middlename',
		'lastname' ,
		'suffix',
		'street',
		'city',
		'region',
		'country_id',
		'postcode',
		'telephone' ,
		'company',
		'fax');
   }
   
   
   public function getSalesPayment()
   {
     return array('method');
   }
   
   
   public function getSalesItem()
   {
     return array(
					'product_sku',
					'product_name',
					'qty_ordered',
					'qty_invoiced',
					'qty_shipped',
					'qty_refunded',
					'qty_canceled',
					'product_type',
					'original_price',
					'base_original_price',
					'row_total',
					'base_row_total',
					'row_weight',
					'price_incl_tax',
					'base_price_incl_tax',
					'product_tax_amount',
					'product_base_tax_amount',
					'product_tax_percent',
					'product_discount',
					'product_base_discount',
					'product_discount_percent',
                    'is_child',
					'product_option' );
   }
   
 	
}