<?php

class Raveinfosys_Exporter_Adminhtml_ExporterController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() 
	{
		$this->loadLayout()
			->_setActiveMenu('exporter/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() 
	{
		$this->_initAction()
			->renderLayout();
	}
	
	public function gridAction() 
	{
		$this->_initAction()
			->renderLayout();
	}

	
	public function newAction() 
	{
		$this->_forward('exportall');
		
	}
	
	public function exportallAction()
	{
	  $orders = Mage::getModel('sales/order')->getCollection()
		->addAttributeToSelect('entity_id');
		$order_arr = array();
		foreach ($orders as $order)  {
				$order_arr[] = $order->getId();
		}
		$file = Mage::getModel('exporter/exportorders')->exportOrders($order_arr);
	    $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
	}

	public function exportCsvAction()
    {
       $orders = $this->getRequest()->getPost('order_ids', array());
	   $file = Mage::getModel('exporter/exportorders')->exportOrders($orders);
	   $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
    }

	public function exportLogAction()
	{
	  $file = 'order_exception_log.htm';
	  $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('var') .'/raveinfosys/exporter/'.$file));
	}
}