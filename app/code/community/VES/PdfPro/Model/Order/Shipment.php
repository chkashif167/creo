<?php
/**
 * VES_PdfPro_Model_Order_Shipment
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Model_Order_Shipment  extends VES_PdfPro_Model_Abstract
{
	protected $_defaultTotalModel = 'pdfpro/sales_order_pdf_total_default';
	protected $_item_model;
	
	public function getItemModel($item){
		return Mage::getModel('pdfpro/order_shipment_item')->setItem($item);
	}
    
    /**
     * Init invoice data
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return array
     */
    
    public function initShipmentData($shipment){
    	$shipmentData	= $shipment->getData();
    	unset($shipmentData['shipping_label']);
		$order 			= $shipment->getOrder();
		$this->setTranslationByStoreId($shipment->getStoreId());
    	$orderData		= Mage::getModel('pdfpro/order')->initOrderData($order);
    	$shipmentData['order']		= unserialize($orderData);
    	$shipmentData['customer']	= $this->getCustomerData(Mage::getModel('customer/customer')->load($order->getCustomerId()));
    	$shipmentData['created_at_formated'] 	= $this->getFormatedDate($shipment->getCreatedAt());
    	$shipmentData['updated_at_formated'] 	= $this->getFormatedDate($shipment->getUpdatedAt());
    	
    	$shipmentData['billing']				= $this->getAddressData($shipment->getBillingAddress());
    	/*if order is not virtual */
    	if(!$order->getIsVirtual()) 
    	$shipmentData['shipping']				= $this->getAddressData($shipment->getShippingAddress());
    	
    	/*Get Payment Info */
    	Mage::getDesign()->setPackageName('default'); /*Set package to default*/
    	$paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->setArea('adminhtml')
            ->toPdf();
        $paymentInfo = str_replace('{{pdf_row_separator}}', "<br />", $paymentInfo);
    	$shipmentData['payment']			= 	array('code'=>$order->getPayment()->getMethodInstance()->getCode(),
    											'name'=>$order->getPayment()->getMethodInstance()->getTitle(),
    											'info'=>$paymentInfo,
    										);
    	$shipmentData['payment_info']		= $paymentInfo;
    	$shipmentData['shipping_description']	= $order->getShippingDescription();
    	/*Get Tracks*/
    	$tracks = array();
    	foreach($shipment->getAllTracks() as $track){
    		$tracks[] = new Varien_Object($track->getData());
    	}
    	
    	$shipmentData['tracking'] 	= sizeof($tracks)?$tracks:false;
    	$shipmentData['items']		= array();
    	$orderCurrencyCode 	= $order->getOrderCurrencyCode();
    	/*
    	 * Get Items information
    	*/
    	
    	foreach($shipment->getAllItems() as $item){
    		if ($item->getOrderItem()->getParentItem()) {
    			continue;
    		}
			$itemModel	= $this->getItemModel($item);
    		if($item->getOrderItem()->getProductType()=='bundle'){
    			$itemData 				= array('is_bundle'=>1,'name'=>$item->getName(),'sku'=>$item->getSku());
    			$itemData['qty']		= $item->getQty() * 1;
    			$itemData['sub_items']	= array();
    			$shipItems 	= $itemModel->getChilds($item);
    			$items 		= array_merge(array($item->getOrderItem()), $item->getOrderItem()->getChildrenItems());
    			foreach ($items as $_item) {
    				$bundleItem = array();
    				$attributes = $itemModel->getSelectionAttributes($_item);
    				// draw SKUs
		            if (!$_item->getParentItem()) {
		                continue;
		            }
    				$bundleItem['label']	= $attributes['option_label'];
    				/*Product name */
    				if ($_item->getParentItem()) {
    					$name = $itemModel->getValueHtml($_item);
    				} else {
    					$name = $_item->getName();
    				}
    				$bundleItem['value']	= $name;
    				$bundleItem['sku']		= $_item->getSku();
	    			if (($itemModel->isShipmentSeparately() && $_item->getParentItem())
		                || (!$itemModel->isShipmentSeparately() && !$_item->getParentItem())
		            ) {
		                if (isset($shipItems[$_item->getId()])) {
		                    $qty = $shipItems[$_item->getId()]->getQty()*1;
		                } else if ($_item->getIsVirtual()) {
		                    $qty = Mage::helper('bundle')->__('N/A');
		                } else {
		                    $qty = 0;
		                }
		            } else {
		                $qty = '';
		            }
		            $bundleItem['qty'] = $qty;
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
    				'subtotal'	=> Mage::helper('pdfpro')->currency($item->getRowTotal(),$orderCurrencyCode)
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
    		$shipmentData['items'][]	= $itemData;
    	}
    	$apiKey = Mage::helper('pdfpro')->getApiKey($order->getStoreId(),$order->getCustomerGroupId());
    	$shipmentData	= new Varien_Object($shipmentData);
    	Mage::dispatchEvent('ves_pdfpro_data_prepare_after',array('source'=>$shipmentData,'model'=>$shipment,'type'=>'shipment'));
    	$shipmentData = new Varien_Object(array('key'=>$apiKey,'data'=>$shipmentData));
    	$this->revertTranslation();
    	return serialize($shipmentData);
    }
}