<?php
/**
 * VES_PdfPro_Model_Order_Invoice
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Model_Order_Invoice  extends VES_PdfPro_Model_Abstract
{
	protected $_defaultTotalModel = 'pdfpro/sales_order_pdf_total_default';
	protected $_item_model;
	
	public function getItemModel($item){
		return Mage::getModel('pdfpro/order_invoice_item')->setItem($item);
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
    
    public function initInvoiceData($invoice){
    	$order = $invoice->getOrder();
    	$orderCurrencyCode 	= $order->getOrderCurrencyCode();
    	$baseCurrencyCode	= $order->getBaseCurrencyCode();
    	$this->setTranslationByStoreId($invoice->getStoreId());
    	$invoiceData 		=	$this->process($invoice->getData(),$orderCurrencyCode,$baseCurrencyCode);
    	$orderData			= 	Mage::getModel('pdfpro/order')->initOrderData($order);
    	
    	$invoiceData['order']	= unserialize($orderData);
    	$invoiceData['customer']= $this->getCustomerData(Mage::getModel('customer/customer')->load($order->getCustomerId()));
    	$invoiceData['created_at_formated'] 	= $this->getFormatedDate($invoice->getCreatedAt());
    	$invoiceData['updated_at_formated'] 	= $this->getFormatedDate($invoice->getUpdatedAt());
    	$invoiceData['billing']					= $this->getAddressData($invoice->getBillingAddress());
    	
    	/*if order is not virtual */
    	if(!$order->getIsVirtual()) 
    	$invoiceData['shipping']			= $this->getAddressData($invoice->getShippingAddress());
    	/*Get Payment Info */
    	Mage::getDesign()->setPackageName('default'); /*Set package to default*/
    	$paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->setArea('adminhtml')
            ->toPdf();
        $paymentInfo = str_replace('{{pdf_row_separator}}', "<br />", $paymentInfo);
    	$invoiceData['payment']			= 	array('code'=>$order->getPayment()->getMethodInstance()->getCode(),
    											'name'=>$order->getPayment()->getMethodInstance()->getTitle(),
    											'info'=>$paymentInfo,
    										);
    	$invoiceData['payment_info']		= $paymentInfo;
    	
    	$invoiceData['shipping_description']	= $order->getShippingDescription();
    	
    	$invoiceData['totals']	= array();
    	$invoiceData['items']	= array();
    	/*
    	 * Get Items information
    	*/
    	foreach($invoice->getAllItems() as $item){
    		if ($item->getOrderItem()->getParentItem()) {
    			continue;
    		}
			$itemModel	= $this->getItemModel($item);
    		if($item->getOrderItem()->getProductType()=='bundle'){
    			$itemData = array('is_bundle'=>1,'name'=>$item->getName(),'sku'=>$item->getSku());
    			if($itemModel->canShowPriceInfo($item)){
    				$itemData['price']	= Mage::helper('pdfpro')->currency($item->getPrice(),$orderCurrencyCode);
    				$itemData['qty']	= $item->getQty() * 1;
    				$itemData['tax']	= Mage::helper('pdfpro')->currency($item->getTaxAmount(),$orderCurrencyCode);
    				$itemData['subtotal']	= Mage::helper('pdfpro')->currency($item->getRowTotal(),$orderCurrencyCode);
    				$itemData['row_total']	= Mage::helper('pdfpro')->currency($item->getRowTotalInclTax(),$orderCurrencyCode);
    			}
    			$itemData['sub_items']	= array();
    			$items = $itemModel->getChilds($item);
    			foreach ($items as $_item) {
    				$bundleItem = array();
    				$attributes = $itemModel->getSelectionAttributes($_item);
    				// draw SKUs
		            if (!$_item->getOrderItem()->getParentItem()) {
		                continue;
		            }
    				$bundleItem['label']	= $attributes['option_label'];
    				/*Product name */
    				if ($_item->getOrderItem()->getParentItem()) {
    					$name = $itemModel->getValueHtml($_item);
    				} else {
    					$name = $_item->getName();
    				}
    				$bundleItem['value']	= $name;
    				/*$bundleItem['sku']		= $_item->getSku();*/
       				/* price */
    				if ($itemModel->canShowPriceInfo($_item)) {
    					$price = $order->formatPriceTxt($_item->getPrice());
    					$bundleItem['price']	= Mage::helper('pdfpro')->currency($_item->getPrice(),$orderCurrencyCode);
    					$bundleItem['qty']		= $_item->getQty() * 1;
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
    				'qty'		=> $item->getQty() * 1,
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
    		$invoiceData['items'][]	= $itemData;
    	}

    	/*
    	 * Get Totals information.
    	*/
    	$totals = $this->_getTotalsList($invoice);
    	$totalArr = array();
    	foreach ($totals as $total) {
    		$total->setOrder($order)
    		->setSource($invoice);
    		if ($total->canDisplay()) {
    			$area = $total->getSourceField()=='grand_total'?'footer':'body';
    			foreach ($total->getTotalsForDisplay() as $totalData) {
    				$totalArr[$area][] = new Varien_Object(array('label'=>$totalData['label'], 'value'=>$totalData['amount']));
    			}
    		}
    	}
    	$invoiceData['totals'] = new Varien_Object($totalArr);
    	$apiKey = Mage::helper('pdfpro')->getApiKey($order->getStoreId(),$order->getCustomerGroupId());
    	
    	$invoiceData 	= new Varien_Object($invoiceData);
    	Mage::dispatchEvent('ves_pdfpro_data_prepare_after',array('source'=>$invoiceData,'model'=>$invoice,'type'=>'invoice'));
    	$invoiceData 	= new Varien_Object(array('key'=>$apiKey,'data'=>$invoiceData));
    	$this->revertTranslation();
    	return serialize($invoiceData);
    }
    
    public function getBasePriceAttributes(){
    	return array(
			'base_grand_total',
			'base_tax_amount',
			'base_shipping_tax_amount',
			'base_discount_amount',
			'base_subtotal_incl_tax',
			'base_shipping_amount',
			'base_subtotal',
			'base_hidden_tax_amount',
			'base_shipping_hidden_tax_amnt',
			'base_shipping_incl_tax',
			'base_total_refunded',
    		'base_cod_fee',
    	);
    }

	public function getPriceAttributes(){
		return array(
			'shipping_tax_amount',
			'tax_amount',
			'grand_total',
			'shipping_amount',
			'subtotal_incl_tax',
			'subtotal',
			'discount_amount',
			'hidden_tax_amount',
			'shipping_hidden_tax_amount',
			'shipping_incl_tax',
			'cod_fee',
		);
	}
}