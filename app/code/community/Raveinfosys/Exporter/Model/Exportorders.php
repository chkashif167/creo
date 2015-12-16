<?php

class Raveinfosys_Exporter_Model_Exportorders extends Raveinfosys_Exporter_Model_Exporter
{
    const ENCLOSURE = '"';
    const DELIMITER = ',';

    
    public function exportOrders($orders)
    {
        $fileName = 'order_export_'.date("Ymd_His").'.csv';
        $fp = fopen(Mage::getBaseDir('export').'/'.$fileName, 'w');

        $this->writeHeadRow($fp);
        foreach ($orders as $order) {
        	$order = Mage::getModel('sales/order')->load($order);
            $this->writeOrder($order, $fp);
        }

        fclose($fp);

        return $fileName;
    }

   
    protected function writeHeadRow($fp)
    {
        fputcsv($fp, $this->getHeadRowValues(), self::DELIMITER, self::ENCLOSURE);
    }

    
    protected function writeOrder($order, $fp)
    {
        $common = $this->getCommonOrderValues($order);
        $blank = $this->getBlankOrderValues($order);
		$orderItems = $order->getItemsCollection();
        $itemInc = 0;
		$data = array();
		$count = 0;
        foreach ($orderItems as $item)
        {
            if($count==0)
				{
                 $record = array_merge($common, $this->getOrderItemValues($item, $order, ++$itemInc));
                 fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE);
				}
				else
				{
				 $record = array_merge($blank, $this->getOrderItemValues($item, $order, ++$itemInc));
                 fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE);
				}
				$count++;
        }
		
    }

    protected function getHeadRowValues()
    {
        return array(
            "order_id",
			"email",
			"firstname",
			"lastname",
			"prefix",
			"middlename",
			"suffix",
			"taxvat",
			"created_at",
			"updated_at",
			"invoice_created_at",
			"shipment_created_at",
			"creditmemo_created_at",
			"tax_amount",
			"base_tax_amount",
			"discount_amount",
			"base_discount_amount",
			"shipping_tax_amount",
			"base_shipping_tax_amount",
			"base_to_global_rate",
			"base_to_order_rate",
			"store_to_base_rate",
			"store_to_order_rate",
			"subtotal_incl_tax",
			"base_subtotal_incl_tax",
			"coupon_code",
			"shipping_incl_tax",
			"base_shipping_incl_tax",
			"shipping_method",
			"shipping_amount",
			"subtotal",
			"base_subtotal",
			"grand_total",
			"base_grand_total",
			"base_shipping_amount",
			"adjustment_positive",
			"adjustment_negative",
			"refunded_shipping_amount",
			"base_refunded_shipping_amount",
			"refunded_subtotal",
			"base_refunded_subtotal",
			"refunded_tax_amount",
			"base_refunded_tax_amount",
			"refunded_discount_amount",
			"base_refunded_discount_amount",
			"store_id",
			"order_status",
			"order_state",
			"hold_before_state",
			"hold_before_status",
			"store_currency_code",
			"base_currency_code",
			"order_currency_code",
			"total_paid",
			"base_total_paid",
			"is_virtual",
			"total_qty_ordered",
			"remote_ip",
			"total_refunded",
			"base_total_refunded",
			"total_canceled",
			"total_invoiced",
			"customer_id",
			"billing_prefix",
			"billing_firstname",
			"billing_middlename",
			"billing_lastname",
			"billing_suffix",
			"billing_street_full",
			"billing_city",
			"billing_region",
			"billing_country",
			"billing_postcode",
			"billing_telephone",
			"billing_company",
			"billing_fax",
			"customer_id",
			"shipping_prefix",
			"shipping_firstname",
			"shipping_middlename",
			"shipping_lastname",
			"shipping_suffix",
			"shipping_street_full",
			"shipping_city",
			"shipping_region",
			"shipping_country",
			"shipping_postcode",
			"shipping_telephone",
			"shipping_company",
			"shipping_fax",
			"payment_method",
			"product_sku",
			"product_name",
			"qty_ordered",
            "qty_invoiced",
            "qty_shipped",
            "qty_refunded",
            "qty_canceled",
            "product_type",
            "original_price",
            "base_original_price",
            "row_total",
            "base_row_total",
            "row_weight",
            "price_incl_tax",
            "base_price_incl_tax",
			"product_tax_amount",
			"product_base_tax_amount",
            "product_tax_percent",
            "product_discount",
            "product_base_discount",
            "product_discount_percent",
            "is_child",
			"product_option"
			
    	);
    }

    //Common orders value
	
    protected function getCommonOrderValues($order)
    {
        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
		$billingAddress = $order->getBillingAddress();
		if(!$shippingAddress)
		$shippingAddress = $billingAddress;
		
		$credit_detail = $this->getCreditMemoDetail($order);
		return array(
            $order->getIncrementId(), 
            $order->getData('customer_email'),
            $this->formatText($order->getData('customer_firstname')),
            $this->formatText($order->getData('customer_lastname')),
            $this->formatText($order->getData('customer_prefix')),
            $this->formatText($order->getData('customer_middlename')),
            $this->formatText($order->getData('customer_suffix')),
            $order->getData('customer_taxvat'),
            $order->getData('created_at'),
            $order->getData('updated_at'),
            $this->getInvoiceDate($order),
            $this->getShipmentDate($order),
            $this->getCreditmemoDate($order),
            $order->getData('tax_amount'),
            $order->getData('base_tax_amount'),
            $order->getData('discount_amount'),
            $order->getData('base_discount_amount'),
            $order->getData('shipping_tax_amount'),
            $order->getData('base_shipping_tax_amount'),
            $order->getData('base_to_global_rate'),
            $order->getData('base_to_order_rate'),
            $order->getData('store_to_base_rate'),
            $order->getData('store_to_order_rate'),
            $order->getData('subtotal_incl_tax'),
            $order->getData('base_subtotal_incl_tax'),
            $order->getData('coupon_code'),
            $order->getData('shipping_incl_tax'),
            $order->getData('base_shipping_incl_tax'),
			$this->getShippingMethod($order),
			$order->getData('shipping_amount'),
			$order->getData('subtotal'),
			$order->getData('base_subtotal'),
			$order->getData('grand_total'),
			$order->getData('base_grand_total'),
			$order->getData('base_shipping_amount'),
			$credit_detail['adjustment_positive'],
			$credit_detail['adjustment_negative'],
			$credit_detail['shipping_amount'],
			$credit_detail['base_shipping_amount'],
			$credit_detail['subtotal'],
			$credit_detail['base_subtotal'],
			$credit_detail['tax_amount'],
			$credit_detail['base_tax_amount'],
			$credit_detail['discount_amount'],
			$credit_detail['base_discount_amount'],
			$order->getData('store_id'),
			$order->getStatus(),
			$order->getState(),
			$order->getHoldBeforeState(),
			$order->getHoldBeforeStatus(),
			$order->getData('store_currency_code'),
			$order->getData('base_currency_code'),
			$order->getData('order_currency_code'),
			$order->getData('total_paid'),
			$order->getData('base_total_paid'),
			$order->getData('is_virtual'),
			$order->getData('total_qty_ordered'),
			$order->getData('remote_ip'),
			$order->getData('total_refunded'),
			$order->getData('base_total_refunded'),
			$order->getData('total_canceled'),
			$order->getData('total_invoiced'),
			$order->getData('customer_id'),
			$this->formatText($order->getBillingAddress()->getData('prefix')),
            $this->formatText($order->getBillingAddress()->getData('firstname')),
            $this->formatText($order->getBillingAddress()->getData('middlename')),
            $this->formatText($order->getBillingAddress()->getData('lastname')),
            $this->formatText($order->getBillingAddress()->getData('suffix')),
            $this->formatText($order->getBillingAddress()->getData('street')),
            $this->formatText($order->getBillingAddress()->getData('city')),
            $this->formatText($order->getBillingAddress()->getData('region')),
            $this->formatText($order->getBillingAddress()->getData('country_id')),
            $order->getBillingAddress()->getData('postcode'),
            $order->getBillingAddress()->getData('telephone'),
            $this->formatText($order->getBillingAddress()->getData('company')),
            $order->getBillingAddress()->getData('fax'),
			$order->getData('customer_id'),
            $shippingAddress->getData('prefix'),
            $this->formatText($shippingAddress->getData('firstname')),
            $this->formatText($shippingAddress->getData('middlename')),
            $this->formatText($shippingAddress->getData('lastname')),
            $this->formatText($shippingAddress->getData('suffix')),
            $this->formatText($shippingAddress->getData('street')),
            $this->formatText($shippingAddress->getData('city')),
            $this->formatText($shippingAddress->getData('region')),
            $shippingAddress->getData('country_id'),
            $shippingAddress->getData('postcode'),
            $shippingAddress->getData('telephone'),
            $this->formatText($shippingAddress->getData('company')),
            $shippingAddress->getData('fax'),
            $this->getPaymentMethod($order)
			);
    }
	
	protected function getBlankOrderValues($order)
    {
       return array(
            '','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',
            '','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',
			'','','','','','','','','','','','','','','','','','','','','','','','','');
    }

    //To return the array of ordered items
    protected function getOrderItemValues($item, $order, $itemInc=1)
    {
		return array(
             $this->getItemSku($item),
             $this->formatText($item->getName()),
			 (int)$item->getQtyOrdered(),
             (int)$item->getQtyInvoiced(),
             (int)$item->getQtyShipped(),
             (int)$item->getQtyRefunded(),
             (int)$item->getQtyCanceled(),
             $item->getProductType(),
             $item->getOriginalPrice(),
             $item->getBaseOriginalPrice(),
             $item->getRowTotal(),
             $item->getBaseRowTotal(),
			 $item->getRowWeight(),
             $item->getPriceInclTax(),
             $item->getBasePriceInclTax(),
             $item->getTaxAmount(),
             $item->getBaseTaxAmount(),
             $item->getTaxPercent(),
             $item->getDiscountAmount(),
             $item->getBaseDiscountAmount(),
             $item->getDiscountPercent(),
			 $this->getChildInfo($item),
			 $item->getdata('product_options')
		);
    }
	
}