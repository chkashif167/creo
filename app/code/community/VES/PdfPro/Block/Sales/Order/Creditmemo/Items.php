<?php
/**
 * VES_PdfPro_Block_Sales_Order_Creditmemo_Items
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Block_Sales_Order_Creditmemo_Items extends Mage_Sales_Block_Order_Creditmemo_Items
{
	public function getPrintCreditmemoUrl($creditmemo)
    {
        if(!Mage::getStoreConfig('pdfpro/config/enabled') || !Mage::getStoreConfig('pdfpro/config/allow_customer_print') || !Mage::helper('pdfpro')->getDefaultApiKey()) return parent::getPrintCreditmemoUrl($creditmemo);
    	if(Mage::getSingleton('customer/session')->isLoggedIn()) return Mage::getUrl('pdfpro/print/creditmemo', array('creditmemo_id' => $creditmemo->getId()));
    	return Mage::getUrl('pdfpro/guest/printCreditmemo', array('creditmemo_id' => $creditmemo->getId()));
    }

    public function getPrintAllCreditmemosUrl($order)
    {
        if(!Mage::getStoreConfig('pdfpro/config/enabled') || !Mage::getStoreConfig('pdfpro/config/allow_customer_print') || !Mage::helper('pdfpro')->getDefaultApiKey()) return parent::getPrintAllCreditmemosUrl($order);
        if(Mage::getSingleton('customer/session')->isLoggedIn()) return Mage::getUrl('pdfpro/print/creditmemos', array('order_id' => $order->getId()));
        return Mage::getUrl('pdfpro/guest/printCreditmemos', array('order_id' => $order->getId()));
    }
}
