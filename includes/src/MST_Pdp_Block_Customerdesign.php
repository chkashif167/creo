<?php
class MST_Pdp_Block_Customerdesign extends Mage_Core_Block_Template
{
	public function __construct()
    {
        parent::__construct();
        $collection = Mage::getModel('pdp/customerdesign')->getCollection();
        //Filter by customer ID
        $customer = Mage::getSingleton("customer/session")->getCustomer();
        $collection->addFieldToFilter("customer_id", $customer->getId());
		$collection->setOrder("id", "desc");
        $this->setCollection($collection);
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
 
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'All'));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
	public function getDesignLink($jsonFilenameId, $productId) {
		$product = Mage::getModel("catalog/product")->load($productId);
		$productUrl = $product->getProductUrl();
		if($productUrl != "") {
			return $productUrl . "?redesign=" . $jsonFilenameId; 
		}
		return "";
	}
}