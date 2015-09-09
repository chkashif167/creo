<?php
/**
 * VES_PdfPro_Block_Sales_Order_Shipment_Items
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Block_Sales_Order_Shipment_Items extends Mage_Sales_Block_Order_Shipment_Items
{
	public function getPrintShipmentUrl($shipment){
        if(!Mage::getStoreConfig('pdfpro/config/enabled') || !Mage::getStoreConfig('pdfpro/config/allow_customer_print') || !Mage::helper('pdfpro')->getDefaultApiKey()) return parent::getPrintShipmentUrl($shipment);
    	if(Mage::getSingleton('customer/session')->isLoggedIn()) return Mage::getUrl('pdfpro/print/shipment', array('shipment_id' => $shipment->getId()));
    	return Mage::getUrl('pdfpro/guest/printShipment', array('shipment_id' => $shipment->getId()));
    }

    public function getPrintAllShipmentsUrl($order){
        if(!Mage::getStoreConfig('pdfpro/config/enabled') || !Mage::getStoreConfig('pdfpro/config/allow_customer_print') || !Mage::helper('pdfpro')->getDefaultApiKey()) return parent::getPrintAllShipmentsUrl($order);
        if(Mage::getSingleton('customer/session')->isLoggedIn()) return Mage::getUrl('pdfpro/print/shipments', array('order_id' => $order->getId()));
        return Mage::getUrl('pdfpro/guest/printShipments', array('order_id' => $order->getId()));
    }
}
