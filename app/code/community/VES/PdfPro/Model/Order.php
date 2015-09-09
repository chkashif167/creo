<?php
/**
 * VES_PdfPro_Model_Order
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Model_Order extends VES_PdfPro_Model_Abstract
{
	protected $_defaultTotalModel = 'pdfpro/sales_order_pdf_total_default';
	protected $_item_model;
	
	public function getItemModel($item){
		return Mage::getModel('pdfpro/order_item')->setItem($item);
	}
	
	/**
	 * Sort totals list
	 *
	 * @param  array $a
	 * @param  array $b
	 * @return int
	 */
	protected function _sortTotalsList($a, $b) {
		if (!isset($a['sort_order']) || !isset($b['sort_order'])) {
			return 0;
		}
	
		if ($a['sort_order'] == $b['sort_order']) {
			return 0;
		}
	
		return ($a['sort_order'] > $b['sort_order']) ? 1 : -1;
	}
    /**
     * Get Total List
     * @param Mage_Sales_Model_Order_Invoice $source
     * @return array
     */
    
	protected function _getTotalsList($source)
    {
        $totals = Mage::getConfig()->getNode('global/pdf/totals')->asArray();
        usort($totals, array($this, '_sortTotalsList'));
        $totalModels = array();
        foreach ($totals as $index => $totalInfo) {
            if (!empty($totalInfo['model'])) {
                $totalModel = Mage::getModel($totalInfo['model']);
                if ($totalModel instanceof Mage_Sales_Model_Order_Pdf_Total_Default) {
                    $totalInfo['model'] = $totalModel;
                } else {
                    Mage::throwException(
                        Mage::helper('sales')->__('PDF total model should extend Mage_Sales_Model_Order_Pdf_Total_Default')
                    );
                }
            } else {
                $totalModel = Mage::getModel($this->_defaultTotalModel);
            }
            $totalModel->setData($totalInfo);
            $totalModels[] = $totalModel;
        }
        return $totalModels;
    }
	
    
    /**
     * Init invoice data
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return array
     */
    public function initOrderData($source){
    	$order 					= $source;
    	$this->setTranslationByStoreId($order->getStoreId());
    	$orderCurrencyCode 		= $order->getOrderCurrencyCode();
    	$baseCurrencyCode		= $order->getBaseCurrencyCode();
    	$sourceData 			=	$this->process($source->getData(),$orderCurrencyCode,$baseCurrencyCode);
    	$sourceData['customer']	= $this->getCustomerData(Mage::getModel('customer/customer')->load($order->getCustomerId()));
    	$sourceData['created_at_formated'] 	= $this->getFormatedDate($source->getCreatedAt());
    	$sourceData['updated_at_formated'] 	= $this->getFormatedDate($source->getUpdatedAt());
    	/*Init gift message*/
    	$sourceData['giftmessage']			= Mage::helper('pdfpro/giftmessage')->initMessage($order);
    	
    	$sourceData['billing']				= $this->getAddressData($source->getBillingAddress());
    	$sourceData['customer_dob']			= isset($sourceData['customer_dob'])?$this->getFormatedDate($sourceData['customer_dob']):'';
    	/*if order is not virtual */
    	if(!$source->getIsVirtual())
    	$sourceData['shipping']				= $this->getAddressData($source->getShippingAddress());
    	
    	/*Get Payment Info */
    	
    	Mage::getDesign()->setPackageName('default'); /*Set package to default*/
    	$paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->setArea('adminhtml')
            ->toPdf();
        $paymentInfo = str_replace('{{pdf_row_separator}}', "<br />", $paymentInfo);
    	$sourceData['payment']			= 	array('code'=>$order->getPayment()->getMethodInstance()->getCode(),
    											'name'=>$order->getPayment()->getMethodInstance()->getTitle(),
    											'info'=>$paymentInfo,
    										);
    	$sourceData['payment_info']		= $paymentInfo;
    	$sourceData['totals']	= array();
    	$sourceData['items']	= array();
    	
    	/*
    	 * Get Items information
    	*/
    	foreach($source->getAllItems() as $item){
    		if ($item->getParentItem()) {
    			continue;
    		}
			$itemModel	= $this->getItemModel($item);
    		if($item->getProductType()=='bundle'){
    			$itemData = array('is_bundle'=>1,'name'=>$item->getName(),'sku'=>$item->getSku());
    			if($itemModel->canShowPriceInfo($item)){
    				$itemData['price']		= Mage::helper('pdfpro')->currency($item->getPrice(),$orderCurrencyCode);
    				$itemData['qty']		= $item->getQtyOrdered() * 1;
    				$itemData['tax']		= Mage::helper('pdfpro')->currency($item->getTaxAmount(),$orderCurrencyCode);
    				$itemData['subtotal']	= Mage::helper('pdfpro')->currency($item->getRowTotal(),$orderCurrencyCode);
    				$itemData['row_total']	= Mage::helper('pdfpro')->currency($item->getRowTotalInclTax(),$orderCurrencyCode);
    			}
    			$items = $itemModel->getChilds($item);
    			$itemData['sub_items']	= array();
    			
    			foreach ($items as $_item) {
    				$bundleItem = array();
    				$attributes = $itemModel->getSelectionAttributes($_item);
    				if(!$attributes['option_label']) continue;
    				$bundleItem['label']	= $attributes['option_label'];
    				/*Product name */
    				if ($_item->getParentItem()) {
    					$name = $itemModel->getValueHtml($_item);
    				} else {
    					$name = $_item->getName();
    				}
    				$bundleItem['value']	= $name;
    				$bundleItem['sku']		= $_item->getSku();
       				/* price */
    				if ($itemModel->canShowPriceInfo($_item)) {
    					$price = $order->formatPriceTxt($_item->getPrice());
    					$bundleItem['price']	= Mage::helper('pdfpro')->currency($_item->getPrice(),$orderCurrencyCode);
    					$bundleItem['qty']		= $_item->getQtyOrdered()*1;
						$bundleItem['tax']		= Mage::helper('pdfpro')->currency($_item->getTaxAmount(),$orderCurrencyCode);
    					$bundleItem['subtotal']	= Mage::helper('pdfpro')->currency($_item->getRowTotal(),$orderCurrencyCode);
    					$bundleItem['row_total']= Mage::helper('pdfpro')->currency($_item->getRowTotalInclTax(),$orderCurrencyCode);
       				}
       				$bundleItem 				= new Varien_Object($bundleItem);
       				Mage::dispatchEvent('ves_pdfpro_data_prepare_after',array('source'=>$bundleItem,'model'=>$_item,'type'=>'item'));
       				$itemData['sub_items'][]	= $bundleItem;
    			}
    		}else{
    			$itemData = array(
    				'name'		=> $item->getName(),
    				'sku'		=> $item->getSku(),
    				'price'		=> Mage::helper('pdfpro')->currency($item->getPrice(),$orderCurrencyCode),
    				'qty'		=> $item->getQtyOrdered() * 1,
    				'tax'		=> Mage::helper('pdfpro')->currency($item->getTaxAmount(),$orderCurrencyCode),
    				'subtotal'	=> Mage::helper('pdfpro')->currency($item->getRowTotal(),$orderCurrencyCode),
    				'row_total'	=> Mage::helper('pdfpro')->currency($item->getRowTotalInclTax(),$orderCurrencyCode)
    			);
    			$options = $itemModel->getItemOptions($item);
    			$itemData['options']	= array();
    			if ($options) {
    				foreach ($options as $option) {
    					$optionData = array();
    					$optionData['label']	= strip_tags($option['label']);
    					 
    					if ($option['value']) {
    						$printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
    						$optionData['value']	= $printValue;
    					}
    					$itemData['options'][] = new Varien_Object($optionData);
    				}
    			}
    		}
    		$itemData	= new Varien_Object($itemData);
    		Mage::dispatchEvent('ves_pdfpro_data_prepare_after',array('source'=>$itemData,'model'=>$item,'type'=>'item'));
    		$sourceData['items'][]	= $itemData;
    	}
    	/*
    	 * Get Totals information.
    	*/
    	$totals = $this->_getTotalsList($source);
    	$totalArr = array();
    	foreach ($totals as $total) {
    		$total->setOrder($order)
    		->setSource($source);
    		if ($total->canDisplay()) {
    			$area = $total->getSourceField()=='grand_total'?'footer':'body';
    			foreach ($total->getTotalsForDisplay() as $totalData) {
    				$totalArr[$area][] = new Varien_Object(array('label'=>$totalData['label'], 'value'=>$totalData['amount']));
    			}
    		}
    	}
    	$sourceData['totals'] = new Varien_Object($totalArr);
    	$apiKey 		= Mage::helper('pdfpro')->getApiKey($order->getStoreId(),$order->getCustomerGroupId());

    	$sourceData 	= new Varien_Object($sourceData);
    	
    	Mage::dispatchEvent('ves_pdfpro_data_prepare_after',array('source'=>$sourceData,'model'=>$order,'type'=>'order'));
    	$orderData 		= new Varien_Object(array('key'=>$apiKey,'data'=>$sourceData));
    	$this->revertTranslation();
    	return serialize($orderData);
    }
    public function getBasePriceAttributes(){
    	return array(
    		'base_discount_amount',
			'base_discount_canceled',
			'base_discount_invoiced',
			'base_discount_refunded',
			'base_grand_total',
			'base_shipping_amount',
			'base_shipping_canceled',
			'base_shipping_invoiced',
			'base_shipping_refunded',
			'base_shipping_tax_amount',
			'base_shipping_tax_refunded',
			'base_subtotal',
			'base_subtotal_canceled',
			'base_subtotal_invoiced',
			'base_subtotal_refunded',
			'base_tax_amount',
			'base_tax_canceled',
			'base_tax_invoiced',
			'base_tax_refunded',
			'base_to_global_rate',
			'base_to_order_rate',
			'base_to_order_rate',
			'base_total_canceled',
			'base_total_invoiced',
			'base_total_invoiced_cost',
			'base_total_offline_refunded',
			'base_total_online_refunded',
			'base_total_paid',
			'base_total_refunded',
    		'base_adjustment_negative',
			'base_adjustment_positive',
			'base_shipping_discount_amount',
			'base_subtotal_incl_tax',
			'base_total_due',
    		'base_shipping_hidden_tax_amnt',
    		'base_hidden_tax_invoiced',
    		'base_hidden_tax_refunded',
    		'base_shipping_incl_tax',
			'base_shipping_hidden_tax_amount',
    		'base_cod_fee'
    	);
    }
    /*Get all price attribute */
	public function getPriceAttributes(){
    	return array(
			'discount_amount',
			'discount_canceled',
			'discount_invoiced',
			'discount_refunded',
			'grand_total',
			'shipping_amount',
			'shipping_canceled',
			'shipping_invoiced',
			'shipping_refunded',
			'shipping_tax_amount',
			'shipping_tax_refunded',
			'store_to_base_rate',
			'subtotal',
			'subtotal_canceled',
			'subtotal_invoiced',
			'subtotal_refunded',
			'tax_amount',
			'tax_canceled',
			'tax_invoiced',
			'tax_refunded',
			'total_canceled',
			'total_invoiced',
			'total_offline_refunded',
			'total_online_refunded',
			'total_paid',
			'total_refunded',
			'adjustment_negative',
			'adjustment_positive',
			'payment_authorization_amount',
			'shipping_discount_amount',
			'subtotal_incl_tax',
			'total_due',
			'hidden_tax_amount',
			'shipping_hidden_tax_amount',
			'hidden_tax_invoiced',
			'hidden_tax_refunded',
			'shipping_incl_tax',
    		'cod_fee',
		);
    }
}