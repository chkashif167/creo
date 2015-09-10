<?php
/**
 * VES_PdfPro_Block_Adminhtml_Sales_Order_Shipment_View
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Block_Adminhtml_Sales_Order_Shipment_View extends Mage_Adminhtml_Block_Template
{
	/**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }
    /**
     * Get Print Url
     * @return string
     */
	public function getPrintUrl(){
		return $this->getUrl('adminhtml/pdfpro_print/shipment',array('shipment_id'=>$this->getShipment()->getId()));
	}
	
	/**
	 * Add PDF Pro Print button to view invoice page
	 * @see Mage_Core_Block_Abstract::_prepareLayout()
	 */
	protected function _prepareLayout(){
		if(!Mage::getStoreConfig('pdfpro/config/enabled')) return;
		$block = $this->getLayout()->getBlock('sales_shipment_view');
		if(!$block) return;
		if(Mage::getStoreConfig('pdfpro/config/remove_default_print')){
			$block->removeButton('print');
		}
		$block->addButton('pdfpro_print', array(
                'label'     => 'Easy PDF - '.Mage::helper('sales')->__('Print Packingslip'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getPrintUrl().'\')'
                )
            );
	}
}