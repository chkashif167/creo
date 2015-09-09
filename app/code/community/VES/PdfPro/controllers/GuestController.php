<?php
/**
 * VES_PdfPro_OrderController
 * @extends Mage_Sales_Controller_Abstract
 * @extends Mage_Core_Controller_Front_Action parent
 * 
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_GuestController extends VES_PdfPro_Controller_Abstract
{
	/**
     * Check order view availability
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $currentOrder = Mage::registry('current_order');
        if ($order->getId() && ($order->getId() === $currentOrder->getId())) {
            return true;
        }
        return false;
    }
	/**
     * Try to load valid order and register it
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadValidOrder($orderId = null)
    {
        return Mage::helper('sales/guest')->loadValidOrder();
    }
    
	public function printOrderAction(){
		if (!$this->_loadValidOrder()) {
            return;
        }
		if(!Mage::getStoreConfig('pdfpro/config/enabled') || !Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		$orderId	= $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);
		
		$orderData = Mage::getModel('pdfpro/order')->initOrderData($order);
		try{
			$result = Mage::helper('pdfpro')->initPdf(array($orderData),'order');
			if($result['success']){
				$this->_prepareDownloadResponse('order'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $result['content']);
			}else{
				throw new Mage_Core_Exception($result['msg']);
			}
		}catch(Exception $e){
			//Mage::getSingleton('core/session')->addError($e->getMessage());
			Mage::logException($e);
			Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your order'));
			$this->_redirect('sales/guest/view' , array('order_id'=>$orderId));
		}
	}
	
    /**
     * Print An Invoice
     */
	public function printInvoiceAction(){
		if(!Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		if (!$this->_loadValidOrder()) {
            return;
        }
		$invoiceId = $this->getRequest()->getParam('invoice_id');						//invoice id
		$invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);				//invoice model with id
		
        if(!$invoice->getId()) {
        	Mage::getSingleton('core/session')->addError($this->__('The invoice is no longer exists.'));
        	$this->_redirect('sales/guest/form');
        	return;
        }
        
        //if can view order
		if($this->_canViewOrder($invoice->getOrder())) {
			$invoiceData = Mage::getModel('pdfpro/order_invoice')->initInvoiceData($invoice);
			try{
				$result = Mage::helper('pdfpro')->initPdf(array($invoiceData));
				if($result['success']){
					$this->_prepareDownloadResponse('invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $result['content']);
				}else{
					throw new Mage_Core_Exception($result['msg']);
				}
			}catch(Exception $e){
				//Mage::getSingleton('core/session')->addError($e->getMessage());
				Mage::logException($e);
				Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your invoice'));
				$this->_redirect('sales/guest/invoice' , array('order_id'=>$invoice->getOrder()->getId()));
			}
		}else {
			$this->_redirect('sales/guest/form');
		}
	}
	
	
	/**
	 * Print invoices
	 */
	public function printInvoicesAction(){
		if(!Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		if (!$this->_loadValidOrder()) {
            return;
        }
		$orderId = $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);		

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
						$this->_prepareDownloadResponse('invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $result['content']);
					}else{
						throw new Mage_Core_Exception($result['msg']);
					}
				}catch(Exception $e) {
					//Mage::getSingleton('core/session')->addError($e->getMessage());
					Mage::logException($e);
					Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your invoices'));
					$this->_redirect('sales/guest/invoice' , array('order_id'=>$orderId));
				}
			}
		}else {
			$this->_redirect('sales/guest/form');
		}
	}
	
	/**
     * Print a shipment
     */
	public function printShipmentAction(){
		if(!Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		if (!$this->_loadValidOrder()) {
            return;
        }
		$shipmentId = $this->getRequest()->getParam('shipment_id');
		$shipment 	= Mage::getModel('sales/order_shipment')->load($shipmentId);
		
        if(!$shipment->getId()) {
        	Mage::getSingleton('core/session')->addError($this->__('The shipment is no longer exists.'));
        	$this->_redirect('sales/guest/form');
        	return;
        }
        
        //if can view order
		if($this->_canViewOrder($shipment->getOrder())) {
			$shipmentData = Mage::getModel('pdfpro/order_shipment')->initShipmentData($shipment);
			try{
				$result = Mage::helper('pdfpro')->initPdf(array($shipmentData),'shipment');
				if($result['success']){
					$this->_prepareDownloadResponse('packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $result['content']);
				}else{
					throw new Mage_Core_Exception($result['msg']);
				}
			}catch(Exception $e){
				//Mage::getSingleton('core/session')->addError($e->getMessage());
				Mage::logException($e);
				Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your shipment'));
				$this->_redirect('sales/guest/shipment',array('order_id'=>$shipment->getOrder()->getId()));
			}
		}else {
			$this->_redirect('sales/guest/form');
		}
	}
	
	
	/**
	 * Print shipments
	 */
	public function printShipmentsAction(){
		if(!Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		if (!$this->_loadValidOrder()) {
            return;
        }
		$orderId = $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);		

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
						$this->_prepareDownloadResponse('packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $result['content']);
					}else{
						throw new Mage_Core_Exception($result['msg']);
					}
				}catch(Exception $e) {
					//Mage::getSingleton('core/session')->addError($e->getMessage());
					Mage::logException($e);
					Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your shipments'));
					$this->_redirect('sales/guest/shipment',array('order_id'=>$orderId));
				}
			}
		}else {
			$this->_redirect('sales/guest/form');
		}
	}
	/**
     * Print a shipment
     */
	public function printCreditmemoAction(){
		if(!Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		if (!$this->_loadValidOrder()) {
            return;
        }
		$creditmemoId = $this->getRequest()->getParam('creditmemo_id');
		$creditmemo 	= Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
		
        if(!$creditmemo->getId()) {
        	Mage::getSingleton('core/session')->addError($this->__('The invoice no longer exists.'));
        	$this->_redirect('sales/guest/form');
        	return;
        }
        
        //if can view order
		if($this->_canViewOrder($creditmemo->getOrder())) {
			$creditmemoData = Mage::getModel('pdfpro/order_creditmemo')->initCreditmemoData($creditmemo);
			try{
				$result = Mage::helper('pdfpro')->initPdf(array($creditmemoData),'creditmemo');
				if($result['success']){
					$this->_prepareDownloadResponse('creditmemo'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $result['content']);
				}else{
					throw new Mage_Core_Exception($result['msg']);
				}
			}catch(Exception $e){
				//Mage::getSingleton('core/session')->addError($e->getMessage());
				Mage::logException($e);
				Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your creditmemo'));
				$this->_redirect('sales/guest/creditmemo',array('order_id'=>$creditmemo->getOrder()->getId()));
			}
		}else {
			$this->_redirect('sales/guest/form');
		}
	}
	
	
	/**
	 * Print shipments
	 */
	public function printCreditmemosAction(){
		if(!Mage::getStoreConfig('pdfpro/config/allow_customer_print')){$this->_forward('no-route');return; }
		if (!$this->_loadValidOrder()) {
            return;
        }
		$orderId = $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);		
		
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
						$this->_prepareDownloadResponse('creditmemo'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $result['content']);
					}else{
						throw new Mage_Core_Exception($result['msg']);
					}
				}catch(Exception $e) {
					//Mage::getSingleton('core/session')->addError($e->getMessage());
					Mage::logException($e);
					Mage::getSingleton('core/session')->addError(Mage::helper('pdfpro')->__('Error: Can not print your creditmemos'));
					$this->_redirect('sales/guest/creditmemo',array('order_id'=>$orderId));
				}
			}
		}else {
			$this->_redirect('sales/guest/form');
		}
	}
}