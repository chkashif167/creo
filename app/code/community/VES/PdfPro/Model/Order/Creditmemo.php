<?php
/**
 * VES_PdfPro_Model_Order_Creditmemo
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Model_Order_Creditmemo extends VES_PdfPro_Model_Abstract
{
	protected $_defaultTotalModel = 'pdfpro/sales_order_pdf_total_default';
	protected $_item_model;
	
	public function getItemModel($item){
		return Mage::getModel('pdfpro/order_creditmemo_item')->setItem($item);
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
    
    public function initCreditmemoData($creditmemo){
		$order 				= $creditmemo->getOrder();
		$this->setTranslationByStoreId($creditmemo->getStoreId());
		$orderCurrencyCode 	= $order->getOrderCurrencyCode();
		$baseCurrencyCode	= $order->getBaseCurrencyCode();
    	$creditmemoData 	=	$this->process($creditmemo->getData(),$orderCurrencyCode,$baseCurrencyCode);
    	$orderData			= Mage::getModel('pdfpro/order')->initOrderData($order);
    	$creditmemoData['order']				= unserialize($orderData);
    	$creditmemoData['customer']	= $this->getCustomerData(Mage::getModel('customer/customer')->load($order->getCustomerId()));
    	$creditmemoData['created_at_formated'] 	= $this->getFormatedDate($creditmemo->getCreatedAt());
    	$creditmemoData['updated_at_formated'] 	= $this->getFormatedDate($creditmemo->getUpdatedAt());
    	
    	$creditmemoData['billing']				= $this->getAddressData($creditmemo->getBillingAddress());
    	/*if order is not virtual */
    	if(!$order->getIsVirtual()) 
    	$creditmemoData['shipping']				= $this->getAddressData($creditmemo->getShippingAddress());
    	/*Get Payment Info */
    	Mage::getDesign()->setPackageName('default'); /*Set package to default*/
    	$paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->setArea('adminhtml')
            ->toPdf();
        $paymentInfo = str_replace('{{pdf_row_separator}}', "<br />", $paymentInfo);
    	$creditmemoData['payment']			= 	array('code'=>$order->getPayment()->getMethodInstance()->getCode(),
    											'name'=>$order->getPayment()->getMethodInstance()->getTitle(),
    											'info'=>$paymentInfo,
    										);
    	$creditmemoData['payment_info']		= $paymentInfo;
    	$creditmemoData['shipping_description']	= $order->getShippingDescription();
    	
    	$creditmemoData['items']		= array();
    	$orderCurrencyCode 	= $order->getOrderCurrencyCode();
    	/*
    	 * Get Items information
    	*/
    	
    	foreach($creditmemo->getAllItems() as $item){
    		if ($item->getOrderItem()->getParentItem()) {
    			continue;
    		}
			$itemModel	= $this->getItemModel($item);
    		if($item->getOrderItem()->getProductType()=='bundle'){
    			$itemData 	= array('is_bundle'=>1,'name'=>$item->getName(),'sku'=>$item->getSku());
    			if($itemModel->canShowPriceInfo($item)){
    				$itemData['price']		= Mage::helper('pdfpro')->currency($item->getPrice(),$orderCurrencyCode);
    				$itemData['qty']		= $item->getQty() * 1;
    				$itemData['tax']		= Mage::helper('pdfpro')->currency($item->getTaxAmount(),$orderCurrencyCode);
    				$itemData['subtotal']	= Mage::helper('pdfpro')->currency($item->getRowTotal(),$orderCurrencyCode);
    				$itemData['row_total']	= Mage::helper('pdfpro')->currency($item->getRowTotalInclTax(),$orderCurrencyCode);
    			}
    			$itemData['sub_items']	= array();
    			$items 	= $itemModel->getChilds($item);
    			//$items 		= array_merge(array($item->getOrderItem()), $item->getOrderItem()->getChildrenItems());
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
    				$bundleItem['sku']		= $_item->getSku();
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
    		$creditmemoData['items'][]	= $itemData;
    	}
    	/*
    	 * Get Totals information.
    	*/
    	$totals = $this->_getTotalsList($creditmemo);
    	$totalArr = array();
    	foreach ($totals as $total) {
    		$total->setOrder($order)
    		->setSource($creditmemo);
    		if ($total->canDisplay()) {
    			$area = $total->getSourceField()=='grand_total'?'footer':'body';
    			foreach ($total->getTotalsForDisplay() as $totalData) {
    				$totalArr[$area][] = new Varien_Object(array('label'=>$totalData['label'], 'value'=>$totalData['amount']));
    			}
    		}
    	}
    	$creditmemoData['totals'] = new Varien_Object($totalArr);
    	$apiKey = Mage::helper('pdfpro')->getApiKey($order->getStoreId(),$order->getCustomerGroupId());
    	
    	$creditmemoData	= new Varien_Object($creditmemoData);
    	Mage::dispatchEvent('ves_pdfpro_data_prepare_after',array('source'=>$creditmemoData,'model'=>$creditmemo,'type'=>'creditmemo'));
    	$creditmemoData = new Varien_Object(array('key'=>$apiKey,'data'=>$creditmemoData));
		$this->revertTranslation();
    	return serialize($creditmemoData);
    }
    
    public function getBasePriceAttributes(){
    	return array(
    		'base_shipping_tax_amount',
			'base_discount_amount',
    		'base_adjustment_negative',
			'base_subtotal_incl_tax',
    		'base_shipping_amount',
			'base_adjustment',
			'base_subtotal',
    		'base_grand_total',
			'base_adjustment_positive',
			'base_tax_amount',
    		'base_hidden_tax_amount',
    		'base_shipping_incl_tax',
    		'base_cod_fee',
    	);
    }
	/*Get all price attribute */
	public function getPriceAttributes(){
    	return array(
			'adjustment_positive',
			'grand_total',
			'shipping_amount',
			'subtotal_incl_tax',
			'adjustment_negative',
			'discount_amount',
			'subtotal',
			'adjustment',
			'shipping_tax_amount',
			'tax_amount',
			'hidden_tax_amount',
			'shipping_incl_tax',
    		'cod_fee',
		);
    }
}