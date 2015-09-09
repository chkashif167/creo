<?php
/**
 * VES_PdfPro_OrderController
 * @extends Mage_Sales_Controller_Abstract
 * @extends Mage_Core_Controller_Front_Action parent
 * 
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */

class VES_PdfPro_PrintController extends VES_PdfPro_Controller_Abstract
{
    
	public function orderAction(){
		if (!$this->_loadValidOrder()) {
            return;
        }
		if(!Mage::getStoreConfig('pdfpro/config/enabled') || !Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		$orderId	= $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);
		if(!$order->getId()){$this->_forward('no-route');return;}
		
		$orderData = Mage::getModel('pdfpro/order')->initOrderData($order);
		try{
			$result = Mage::helper('pdfpro')->initPdf(array($orderData),'order');
			if($result['success']){
				$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('order',$order).'.pdf', $result['content']);
			}else{
				throw new Mage_Core_Exception($result['msg']);
			}
		}catch(Exception $e){
			//Mage::getSingleton('core/session')->addError($e->getMessage());
			Mage::logException($e);
			Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your order'));
			$this->_redirect('sales/order/view' , array('order_id'=>$orderId));
		}
	}
	
    /**
     * Print An Invoice
     */
	public function invoiceAction(){
		if(!Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		
		$invoiceId = $this->getRequest()->getParam('invoice_id');						//invoice id
		$invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);				//invoice model with id
		
        if(!$invoice->getId()) {
        	Mage::getSingleton('core/session')->addError($this->__('The invoice no longer exists.'));
        	if(Mage::getSingleton('customer/session')->isLoggedIn()) {
        		$this->_redirect('sales/order/history');
        	}else {
        		$this->_redirect('sales/guest/form');
        	}
        	return;
        }
        
        //if can view order
		if($this->_canViewOrder($invoice->getOrder())) {
			$invoiceData = Mage::getModel('pdfpro/order_invoice')->initInvoiceData($invoice);
			try{
				$result = Mage::helper('pdfpro')->initPdf(array($invoiceData));
				if($result['success']){
					$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('invoice',$invoice).'.pdf', $result['content']);
				}else{
					throw new Mage_Core_Exception($result['msg']);
				}
			}catch(Exception $e){
				//Mage::getSingleton('core/session')->addError($e->getMessage());
				Mage::logException($e);
				Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your invoice'));
				$this->_redirect('sales/order/invoice' , array('order_id'=>$invoice->getOrder()->getId()));
			}
		}else {
			if (Mage::getSingleton('customer/session')->isLoggedIn()) {
				Mage::getSingleton('core/session')->addError($this->__('You don\'t have that invoice.'));
				$this->_redirect('sales/order/history');
			} else {
				Mage::getSingleton('core/session')->addError($this->__('You are not login. Please login to try.'));
				$this->_redirect('sales/guest/form');
			}
		}
	}
	
	
	/**
	 * Print invoices
	 */
	public function invoicesAction(){
		if(!Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		//$flag = false;
		$orderId = $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);		
		
		//order not exist
		if(!$order->getId()) {
			Mage::getSingleton('core/session')->addError($this->__('The order no longer exists.'));
			if(Mage::getSingleton('customer/session')->isLoggedIn()) {
				//login
				$this->_redirect('sales/order/history');
			}else {
				//not login
				$this->_redirect('sales/guest/form');
			}
			return;
		}
		
		//if can view order
		if($this->_canViewOrder($order)) {
			$invoices = Mage::getResourceModel('sales/order_invoice_collection')->addAttributeToSelect('*')
			->setOrderFilter($orderId)
			->load();	//invoices data from order
			
			if($invoices->count() > 0) {
				$invoiceDatas = array();
				foreach($invoices as $invoice) {
					$invoiceDatas[] = Mage::getModel('pdfpro/order_invoice')->initInvoiceData($invoice);
				}
				try{
					$result = Mage::helper('pdfpro')->initPdf($invoiceDatas);
					if($result['success']){
						$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('invoices').'.pdf', $result['content']);
					}else{
						throw new Mage_Core_Exception($result['msg']);
					}
				}catch(Exception $e) {
					//Mage::getSingleton('core/session')->addError($e->getMessage());
					Mage::logException($e);
					Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your invoices'));
					$this->_redirect('sales/order/invoice' , array('order_id'=>$orderId));
				}
			}
		}else {
			//login-not have order
			if (Mage::getSingleton('customer/session')->isLoggedIn()) {
				Mage::getSingleton('core/session')->addError($this->__('You don\'t have that order.'));
				$this->_redirect('sales/order/history');
			} else {
				//not login-not have order
				Mage::getSingleton('core/session')->addError($this->__('You are not login. Please login to try.'));
				$this->_redirect('sales/guest/form');
			}
		}
	}
	
	/**
     * Print a shipment
     */
	public function shipmentAction(){
		if(!Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		
		$shipmentId = $this->getRequest()->getParam('shipment_id');
		$shipment 	= Mage::getModel('sales/order_shipment')->load($shipmentId);
		
        if(!$shipment->getId()) {
        	Mage::getSingleton('core/session')->addError($this->__('The invoice no longer exists.'));
        	if(Mage::getSingleton('customer/session')->isLoggedIn()) {
        		$this->_redirect('sales/order/history');
        	}else {
        		$this->_redirect('sales/guest/form');
        	}
        	return;
        }
        
        //if can view order
		if($this->_canViewOrder($shipment->getOrder())) {
			$shipmentData = Mage::getModel('pdfpro/order_shipment')->initShipmentData($shipment);
			try{
				$result = Mage::helper('pdfpro')->initPdf(array($shipmentData),'shipment');
				if($result['success']){
					$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('shipment',$shipment).'.pdf', $result['content']);
				}else{
					throw new Mage_Core_Exception($result['msg']);
				}
			}catch(Exception $e){
				//Mage::getSingleton('core/session')->addError($e->getMessage());
				Mage::logException($e);
				Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your shipment'));
				$this->_redirect('sales/order/shipment',array('order_id'=>$shipment->getOrder()->getId()));
			}
		}else {
			if (Mage::getSingleton('customer/session')->isLoggedIn()) {
				Mage::getSingleton('core/session')->addError($this->__('You don\'t have that shipment.'));
				$this->_redirect('sales/order/history');
			} else {
				Mage::getSingleton('core/session')->addError($this->__('You are not login. Please login to try.'));
				$this->_redirect('sales/guest/form');
			}
		}
	}
	
	
	/**
	 * Print shipments
	 */
	public function shipmentsAction(){
		if(!Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		//$flag = false;
		$orderId = $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);		
		
		//order not exist
		if(!$order->getId()) {
			Mage::getSingleton('core/session')->addError($this->__('The order no longer exists.'));
			if(Mage::getSingleton('customer/session')->isLoggedIn()) {
				//login
				$this->_redirect('sales/order/history');
			}else {
				//not login
				$this->_redirect('sales/guest/form');
			}
			return;
		}
		
		//if can view order
		if($this->_canViewOrder($order)) {
			$shipments = Mage::getResourceModel('sales/order_shipment_collection')->addAttributeToSelect('*')
			->setOrderFilter($orderId)
			->load();	//invoices data from order
			
			if($shipments->count() > 0) {
				$shipmentDatas = array();
				foreach($shipments as $shipment) {
					$shipmentDatas[] = Mage::getModel('pdfpro/order_shipment')->initShipmentData($shipment);
				}
				try{
					$result = Mage::helper('pdfpro')->initPdf($shipmentDatas,'shipment');
					if($result['success']){
						$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('shipments').'.pdf', $result['content']);
					}else{
						throw new Mage_Core_Exception($result['msg']);
					}
				}catch(Exception $e) {
					//Mage::getSingleton('core/session')->addError($e->getMessage());
					Mage::logException($e);
					Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your shipments'));
					$this->_redirect('sales/order/shipment',array('order_id'=>$orderId));
				}
			}
		}else {
			//login-not have order
			if (Mage::getSingleton('customer/session')->isLoggedIn()) {
				Mage::getSingleton('core/session')->addError($this->__('You don\'t have that order.'));
				$this->_redirect('sales/order/history');
			} else {
				//not login-not have order
				Mage::getSingleton('core/session')->addError($this->__('You are not login. Please login to try.'));
				$this->_redirect('sales/guest/form');
			}
		}
	}
	/**
     * Print a shipment
     */
	public function creditmemoAction(){
		if(!Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		
		$creditmemoId = $this->getRequest()->getParam('creditmemo_id');
		$creditmemo 	= Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
		
        if(!$creditmemo->getId()) {
        	Mage::getSingleton('core/session')->addError($this->__('The invoice no longer exists.'));
        	if(Mage::getSingleton('customer/session')->isLoggedIn()) {
        		$this->_redirect('sales/order/history');
        	}else {
        		$this->_redirect('sales/guest/form');
        	}
        	return;
        }
        
        //if can view order
		if($this->_canViewOrder($creditmemo->getOrder())) {
			$creditmemoData = Mage::getModel('pdfpro/order_creditmemo')->initCreditmemoData($creditmemo);
			try{
				$result = Mage::helper('pdfpro')->initPdf(array($creditmemoData),'creditmemo');
				if($result['success']){
					$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('creditmemo',$creditmemo).'.pdf', $result['content']);
				}else{
					throw new Mage_Core_Exception($result['msg']);
				}
			}catch(Exception $e){
				//Mage::getSingleton('core/session')->addError($e->getMessage());
				Mage::logException($e);
				Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your creditmemo'));
				$this->_redirect('sales/order/creditmemo',array('order_id'=>$creditmemo->getOrder()->getId()));
			}
		}else {
			if (Mage::getSingleton('customer/session')->isLoggedIn()) {
				Mage::getSingleton('core/session')->addError($this->__('You don\'t have that shipment.'));
				$this->_redirect('sales/order/history');
			} else {
				Mage::getSingleton('core/session')->addError($this->__('You are not login. Please login to try.'));
				$this->_redirect('sales/guest/form');
			}
		}
	}
	
	
	/**
	 * Print shipments
	 */
	public function creditmemosAction(){
		if(!Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		//$flag = false;
		$orderId = $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);		
		
		//order not exist
		if(!$order->getId()) {
			Mage::getSingleton('core/session')->addError($this->__('The order no longer exists.'));
			if(Mage::getSingleton('customer/session')->isLoggedIn()) {
				//login
				$this->_redirect('sales/order/history');
			}else {
				//not login
				$this->_redirect('sales/guest/form');
			}
			return;
		}
		
		//if can view order
		if($this->_canViewOrder($order)) {
			$creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')->addAttributeToSelect('*')
			->setOrderFilter($orderId)
			->load();	//invoices data from order
			
			if($creditmemos->count() > 0) {
				$creditmemoDatas = array();
				foreach($creditmemos as $creditmemo) {
					$creditmemoDatas[] = Mage::getModel('pdfpro/order_creditmemo')->initCreditmemoData($creditmemo);
				}
				try{
					$result = Mage::helper('pdfpro')->initPdf($creditmemoDatas,'creditmemo');
					if($result['success']){
						$this->_prepareDownloadResponse(Mage::helper('pdfpro')->getFileName('creditmemos').'.pdf', $result['content']);
					}else{
						throw new Mage_Core_Exception($result['msg']);
					}
				}catch(Exception $e) {
					//Mage::getSingleton('core/session')->addError($e->getMessage());
					Mage::logException($e);
					Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your credit memos'));
					$this->_redirect('sales/order/creditmemo',array('order_id'=>$orderId));
				}
			}
		}else {
			//login-not have order
			if (Mage::getSingleton('customer/session')->isLoggedIn()) {
				Mage::getSingleton('core/session')->addError($this->__('You don\'t have that order.'));
				$this->_redirect('sales/order/history');
			} else {
				//not login-not have order
				Mage::getSingleton('core/session')->addError($this->__('You are not login. Please login to try.'));
				$this->_redirect('sales/guest/form');
			}
		}
	}
}