<?php
/**
 * VES_PdfPro_Block_Adminhtml_Sales_Order_Invoice_View
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Template
{
	/**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }
    /**
     * Get Print Url
     * @return string
     */
	public function getPrintUrl(){
		return $this->getUrl('adminhtml/pdfpro_print/order',array('order_id'=>$this->getOrder()->getId()));
	}
	
	/**
	 * Add PDF Pro Print button to view invoice page
	 * @see Mage_Core_Block_Abstract::_prepareLayout()
	 */
	protected function _prepareLayout(){
		if(!Mage::getStoreConfig('pdfpro/config/enabled') || !Mage::getStoreConfig('pdfpro/config/admin_print_order')) return;
		$block = $this->getLayout()->getBlock('sales_order_edit');
		if($block) $block->addButton('pdfpro_print', array(
                'label'     => 'Easy PDF - '.Mage::helper('sales')->__('Print Order'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getPrintUrl().'\')'
                )
        );
	}
}