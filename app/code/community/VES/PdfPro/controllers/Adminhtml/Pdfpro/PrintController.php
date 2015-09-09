<?php
/**
 * VES_PdfPro_Adminhtml_PrintController
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */

class VES_PdfPro_Adminhtml_Pdfpro_PrintController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Print an Order
	 */
	public function orderAction(){
		$orderId = $this->getRequest()->getParam('order_id');
		if (empty($orderId)) {
			Mage::getSingleton('adminhtml/session')->addError('There is no order to process');
			$this->_redirect('adminhtml/sales_order');
			return;
		}
		
		$order = Mage::getModel('sales/order')->load($orderId);
		if(!$order->getId()){$this->_forward('no-route');return;}
		$orderData= Mage::getModel('pdfpro/order')->initOrderData($order);
		try{
			$result = Mage::helper('pdfpro')->initPdf(array($orderData),'order');
			if($result['success']){
				$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('order',$order).'.pdf', $result['content']);
			}else{
				throw new Mage_Core_Exception($result['msg']);
			}
		}catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
			$this->_redirect('adminhtml/sales_order/view',array('order_id'=>$orderId));
		}
	}
	
	/**
	 * Print Orders
	 */
	public function ordersAction(){
		$orderIds = $this->getRequest()->getParam('order_ids');
		if (empty($orderIds)) {
			Mage::getSingleton('adminhtml/session')->addError('There is no order to process');
			$this->_redirect('adminhtml/sales_order');
			return;
		}
		$orderDatas	= array();
		foreach($orderIds as $orderId){
			$order = Mage::getModel('sales/order')->load($orderId);
			if(!$order->getId()){continue;}
			$orderDatas[] = Mage::getModel('pdfpro/order')->initOrderData($order);
		}
		try{
			$result = Mage::helper('pdfpro')->initPdf($orderDatas,'order');
			if($result['success']){
				$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('orders').'.pdf', $result['content']);
			}else{
				throw new Mage_Core_Exception($result['msg']);
			}
		}catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
			$this->_redirect('adminhtml/sales_order/index');
		}
	}
	
	/**
	 * Print Orders
	 */
	public function customAction(){
		$orderIds = $this->getRequest()->getParam('order_ids');
		if (empty($orderIds)) {
			Mage::getSingleton('adminhtml/session')->addError('There is no order to process');
			$this->_redirect('adminhtml/sales_order');
			return;
		}
		$orderDatas	= array();
		foreach($orderIds as $orderId){
			$order = Mage::getModel('sales/order')->load($orderId);
			if(!$order->getId()){continue;}
			$orderDatas[] = Mage::getModel('pdfpro/order')->initOrderData($order);
		}
		try{
			$type = $this->getRequest()->getParam('type','order');
			$result = Mage::helper('pdfpro')->initPdf($orderDatas,$type);
			if($result['success']){
				$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName($type).'.pdf', $result['content']);
			}else{
				throw new Mage_Core_Exception($result['msg']);
			}
		}catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
			$this->_redirect('adminhtml/sales_order/index');
		}
	}
    
    /**
     * Print An Invoice
     */
	public function invoiceAction(){
		$invoiceId = $this->getRequest()->getParam('invoice_id');
		$invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
        if (!$invoice->getId()) {
        	$this->_getSession()->addError($this->__('The invoice no longer exists.'));
            $this->_forward('no-route');
            return;
		}
		$invoiceData = Mage::getModel('pdfpro/order_invoice')->initInvoiceData($invoice);
        try{
	        $result = Mage::helper('pdfpro')->initPdf(array($invoiceData));
        	if($result['success']){
				$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('invoice',$invoice).'.pdf', $result['content']);
			}else{
				throw new Mage_Core_Exception($result['msg']);
			}
        }catch(Exception $e){
        	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        	$this->_redirect('adminhtml/sales_order_invoice/view',array('invoice_id'=>$invoiceId));
        }
	}
	
	
	/**
	 * Print invoices
	 */
	public function invoicesAction(){
		$orderIds = $this->getRequest()->getParam('order_ids');
		$invoiceIds = $this->getRequest()->getParam('invoice_ids');
		$flag = false;
		if (!empty($orderIds) || !empty($invoiceIds)) {
			$invoiceDatas = array();
			$invoices = Mage::getResourceModel('sales/order_invoice_collection')->addAttributeToSelect('*');
			if(!empty($orderIds)){
				$invoices->addFieldToFilter('order_id',array('in',$orderIds));
			}else if(!empty($invoiceIds)){
				$invoices->addFieldToFilter('entity_id',array('in',$invoiceIds));
			}
			$invoices->load();
			if($invoices->count() > 0) $flag = true;
			foreach($invoices as $invoice){
				$invoiceDatas[] = Mage::getModel('pdfpro/order_invoice')->initInvoiceData($invoice);
			}
			if ($flag) {
				try{
					$result = Mage::helper('pdfpro')->initPdf($invoiceDatas);
		        	if($result['success']){
						$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('invoices').'.pdf', $result['content']);
					}else{
						throw new Mage_Core_Exception($result['msg']);
					}
				}catch(Exception $e){
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					if(!empty($orderIds)) $this->_redirect('adminhtml/sales_order/index');
					else $this->_redirect('adminhtml/sales_invoice/index');
				}
			} else {
				if(!empty($orderIds)){
					$this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
				}else{
					$this->_getSession()->addError($this->__('There are no printable documents related to selected invoices.'));
				}
				if(!empty($orderIds)) $this->_redirect('adminhtml/sales_order/index');
				else $this->_redirect('adminhtml/sales_invoice/index');
			}
		}
	}
	
	/**
	 * Print A Packingslip
	 */
	public function shipmentAction(){
		$shipmentId = $this->getRequest()->getParam('shipment_id');
		$shipment 	= Mage::getModel('sales/order_shipment')->load($shipmentId);
        if (!$shipment->getId()) {
        	$this->_getSession()->addError($this->__('The shipment no longer exists.'));
            $this->_forward('no-route');
            return;
		}
		$shipmentData = Mage::getModel('pdfpro/order_shipment')->initShipmentData($shipment);
        try{
	        $result = Mage::helper('pdfpro')->initPdf(array($shipmentData),'shipment');
        	if($result['success']){
				$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('shipment',$shipment).'.pdf', $result['content']);
			}else{
				throw new Mage_Core_Exception($result['msg']);
			}
        }catch(Exception $e){
        	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        	$this->_redirect('adminhtml/sales_order_shipment/view',array('shipment_id'=>$shipmentId));
        }
	}
	
	/**
	 * Print Packingslips
	 */
	public function shipmentsAction(){
		$orderIds = $this->getRequest()->getPost('order_ids');
		$shipmentIds = $this->getRequest()->getParam('shipment_ids');
        if (!empty($orderIds) || !empty($shipmentIds)) {
        	$shipments = Mage::getResourceModel('sales/order_shipment_collection')->addAttributeToSelect('*');
        	if(!empty($orderIds)){
				$shipments->addFieldToFilter('order_id',array('in',$orderIds));
			}else if(!empty($shipmentIds)){
				$shipments->addFieldToFilter('entity_id',array('in',$shipmentIds));
			}
			$shipments->load();
            if ($shipments->getSize()) {
            	$shipmentDatas = array();
	            foreach($shipments as $shipment){
					$shipmentDatas[] = Mage::getModel('pdfpro/order_shipment')->initShipmentData($shipment);
				}
	            try{
			        $result = Mage::helper('pdfpro')->initPdf($shipmentDatas,'shipment');
		        	if($result['success']){
						$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('shipments').'.pdf', $result['content']);
					}else{
						throw new Mage_Core_Exception($result['msg']);
					}
		        }catch(Exception $e){
		        	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		        	if(!empty($orderIds)) $this->_redirect('adminhtml/sales_order/index');
					else $this->_redirect('adminhtml/sales_shipment/index');
		        }
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                if(!empty($orderIds)) $this->_redirect('adminhtml/sales_order/index');
				else $this->_redirect('adminhtml/sales_shipment/index');
            }
        }
	}
	
/**
	 * Print A Packingslip
	 */
	public function creditmemoAction(){
		$creditmemoId = $this->getRequest()->getParam('creditmemo_id');
		$creditmemo 	= Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
        if (!$creditmemo->getId()) {
        	$this->_getSession()->addError($this->__('The shipment no longer exists.'));
            $this->_forward('no-route');
            return;
		}
		$creditmemoData = Mage::getModel('pdfpro/order_creditmemo')->initCreditmemoData($creditmemo);
        try{
	        $result = Mage::helper('pdfpro')->initPdf(array($creditmemoData),'creditmemo');
        	if($result['success']){
				$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('creditmemo',$creditmemo).'.pdf', $result['content']);
			}else{
				throw new Mage_Core_Exception($result['msg']);
			}
        }catch(Exception $e){
        	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        	$this->_redirect('adminhtml/sales_order_creditmemo/view',array('creditmemo_id'=>$creditmemoId));
        }
	}
	
	/**
	 * Print Packingslips
	 */
	public function creditmemosAction(){
		$orderIds = $this->getRequest()->getPost('order_ids');
		$creditmemoIds = $this->getRequest()->getParam('creditmemo_ids');
        if (!empty($orderIds) || !empty($creditmemoIds)) {
        	$creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')->addAttributeToSelect('*');
        	if(!empty($orderIds)){
				$creditmemos->addFieldToFilter('order_id',array('in',$orderIds));
			}else if(!empty($creditmemoIds)){
				$creditmemos->addFieldToFilter('entity_id',array('in',$creditmemoIds));
			}
			$creditmemos->load();
            if ($creditmemos->getSize()) {
            	$creditmemoDatas = array();
	            foreach($creditmemos as $creditmemo){
					$creditmemoDatas[] = Mage::getModel('pdfpro/order_creditmemo')->initCreditmemoData($creditmemo);
				}
	            try{
			        $result = Mage::helper('pdfpro')->initPdf($creditmemoDatas,'creditmemo');
		        	if($result['success']){
						$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('creditmemos').'.pdf', $result['content']);
					}else{
						throw new Mage_Core_Exception($result['msg']);
					}
		        }catch(Exception $e){
		        	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		        	if(!empty($orderIds)) $this->_redirect('adminhtml/sales_order/index');
					else $this->_redirect('adminhtml/sales_creditmemo/index');
		        }
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                if(!empty($orderIds)) $this->_redirect('adminhtml/sales_order/index');
				else $this->_redirect('adminhtml/sales_creditmemo/index');
            }
        }
	}
	/**
	 * Print All
	 */
	public function allAction(){
		$orderIds = $this->getRequest()->getPost('order_ids');
		$data = array();
		$canPrint = false;
		foreach($orderIds as $orderId){
			$item = array();
			$order = Mage::getModel('sales/order')->load($orderId);
			/*Init order data*/
			if(Mage::getStoreConfig('pdfpro/config/admin_print_order')){
				$item['order'][]	= Mage::getModel('pdfpro/order')->initOrderData($order);
				$canPrint = true;
			}
			/*Init invoice data*/
			$invoices = Mage::getResourceModel('sales/order_invoice_collection')->addAttributeToSelect('*')->addFieldToFilter('order_id',$orderId);
			if($invoices->count() > 0){
				$invoiceDatas = array();
				foreach($invoices as $invoice){
					$invoiceDatas[] = Mage::getModel('pdfpro/order_invoice')->initInvoiceData($invoice);
				}
				$item['invoice']	= $invoiceDatas;
				$canPrint = true;
			}
			
			/*Init shipment data*/
			$shipments = Mage::getResourceModel('sales/order_shipment_collection')->addAttributeToSelect('*')->addFieldToFilter('order_id',$orderId);
			if($shipments->count() > 0){
				$shipmentDatas = array();
	            foreach($shipments as $shipment){
					$shipmentDatas[] = Mage::getModel('pdfpro/order_shipment')->initShipmentData($shipment);
				}
				$item['shipment']	= $shipmentDatas;
				$canPrint = true;
			}
			
			/*Init credit memo data*/
			$creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')->addAttributeToSelect('*')->addFieldToFilter('order_id',$orderId);
			if($creditmemos->count() > 0){
            	$creditmemoDatas = array();
	            foreach($creditmemos as $creditmemo){
					$creditmemoDatas[] = Mage::getModel('pdfpro/order_creditmemo')->initCreditmemoData($creditmemo);
				}
				$item['creditmemo']	= $creditmemoDatas;
				$canPrint = true;
			}
			$data[] = $item;
		}
		try{
			if(!$canPrint){throw new Mage_Core_Exception($this->__('There are no printable documents related to selected orders.'));}
	        $result = Mage::helper('pdfpro')->initPdf($data,'all');
        	if($result['success']){
				$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('all').'.pdf', $result['content']);
			}else{
				throw new Mage_Core_Exception($result['msg']);
			}
        }catch(Exception $e){
        	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        	$this->_redirect('adminhtml/sales_order/index');
        }
	}
}