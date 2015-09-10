<?php
/**
 * VES_PdfPro_Block_Sales_Order_Info_Buttons
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Block_Sales_Order_Info_Buttons extends Mage_Sales_Block_Order_Info_Buttons
{
     /**
     * Get url for printing order
     *
     * @param Mage_Sales_Order $order
     * @return string
     */
    public function getPrintUrl($order)
    {
        if(!Mage::getStoreConfig('pdfpro/config/enabled') || !Mage::getStoreConfig('pdfpro/config/allow_customer_print') || !Mage::helper('pdfpro')->getDefaultApiKey()) return parent::getPrintUrl($order);
        if(Mage::getSingleton('customer/session')->isLoggedIn()) return $this->getUrl('pdfpro/print/order', array('order_id' => $order->getId()));
        return $this->getUrl('pdfpro/guest/printOrder', array('order_id' => $order->getId()));
    }
}
