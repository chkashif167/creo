<?php
class MST_Pdp_Block_Pdptemplate extends Mage_Core_Block_Template
{
	public function __construct() {
		return parent::__construct();
	}
	public function getCustomerId () {
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customerData = Mage::getSingleton('customer/session')->getCustomer();
			return $customerData->getId();
			
		}
	}
	public function getCustomerDesignTemplates () {
		$collection = Mage::getModel('pdp/pdptemplate')->getCollection();
		$collection->addFieldToFilter('customer_id', $this->getCustomerId());
		$collection->setOrder('update_time', 'DESC');
		return $collection;
	}
}