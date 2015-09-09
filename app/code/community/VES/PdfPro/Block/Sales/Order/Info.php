<?php

/**
 * VES_PdfPro_Block_Sales_Order_Info
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Block_Sales_Order_Info extends Mage_Sales_Block_Order_Info
{
    public function getPrintUrl($order)
    {
        if(!Mage::getStoreConfig('pdfpro/config/enabled') || !Mage::getStoreConfig('pdfpro/config/allow_customer_print') || !Mage::helper('pdfpro')->getDefaultApiKey()) return parent::getPrintUrl($order);
        if(Mage::getSingleton('customer/session')->isLoggedIn()) return $this->getUrl('pdfpro/print/order', array('order_id' => $order->getId()));
        return $this->getUrl('pdfpro/guest/printOrder', array('order_id' => $order->getId()));
    }

}
