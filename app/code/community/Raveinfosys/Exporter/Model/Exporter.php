<?php

class Raveinfosys_Exporter_Model_Exporter extends Mage_Core_Model_Abstract
{
    public function getPaymentMethod($order)
	{
		return $order->getPayment()->getMethod();
	}

	public function getChildInfo($item)
	{
	  
	  if($item->getParentItemId())
	  return 'yes';
	  else
	  return 'no';
	  
	}

    public function getShippingMethod($order)
    {
        if (!$order->getIsVirtual() && $order->getShippingDescription()) {
            return $order->getShippingDescription();
        }
        else if (!$order->getIsVirtual() && $order->getShippingMethod()) {
        	return $order->getShippingMethod();
        }
        return '';
    }

    public function getItemSku($item)
    {
        if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $item->getProductOptionByCode('simple_sku');
        }
        return $item->getSku();
    }

    public function formatText($string)
    {
        $string = str_replace(',', ' ', $string);
		return $string;
    }
	
	public function getStoreIds()
    {
       $collection = Mage::getModel('core/store')->getCollection();
	   $store_ids = array();
	   $i = 0;
	   foreach($collection as $data)
	   {
	    $store_ids[$i]['label'] = $data['name'];
	    $store_ids[$i]['value'] = $data['store_id'];
		$i++;
	   }
		return $store_ids;
    }
	
	public function getCreditMemoDetail($order)
    {
	    $credit_detail['adjustment_positive'] = 0;
	    $credit_detail['adjustment_negative'] = 0;
	    $credit_detail['shipping_amount'] = 0;
	    $credit_detail['base_shipping_amount'] = 0;
	    $credit_detail['subtotal'] = 0;
	    $credit_detail['base_subtotal'] = 0;
	    $credit_detail['tax_amount'] = 0;
	    $credit_detail['base_tax_amount'] = 0;
	    $credit_detail['discount_amount'] = 0;
	    $credit_detail['base_discount_amount'] = 0;
        $collection = $order->getCreditmemosCollection();
		if(count($collection))
		{
		 foreach($collection as $data)
		 {
		  $credit_detail['adjustment_positive'] += $data->getData('adjustment_positive');
		  $credit_detail['adjustment_negative'] += $data->getData('adjustment_negative');
		  $credit_detail['shipping_amount'] += $data->getData('shipping_amount');
		  $credit_detail['base_shipping_amount'] += $data->getData('base_shipping_amount');
		  $credit_detail['subtotal'] += $data->getData('subtotal');
		  $credit_detail['base_subtotal'] += $data->getData('base_subtotal');
		  $credit_detail['tax_amount'] += $data->getData('tax_amount');
		  $credit_detail['base_tax_amount'] += $data->getData('base_tax_amount');
		  $credit_detail['discount_amount'] += $data->getData('discount_amount');
		  $credit_detail['base_discount_amount'] += $data->getData('base_discount_amount');
		 }
		}
		
		return $credit_detail;
		
    }
	
	public function getInvoiceDate($order)
    {
	    $date = '';
        $collection = $order->getInvoiceCollection();
		if(count($collection))
		{
		 foreach($collection as $data)
		 $date = $data->getData('created_at');
		}
		
		return $date;
		
    }
	
	public function getShipmentDate($order)
    {
	    $date = '';
        $collection = $order->getShipmentsCollection();
		if(count($collection))
		{
		 foreach($collection as $data)
		 $date = $data->getData('created_at');
		}
		
		return $date;
		
    }
	
	public function getCreditmemoDate($order)
    {
	    $date = '';
        $collection = $order->getCreditmemosCollection();
		if(count($collection))
		{
		 foreach($collection as $data)
		 $date = $data->getData('created_at');
		}
		
		return $date;
		
    }

	
}