<?php
/**
 * VES_PdfPro_Helper_Giftmessage
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Helper_Giftmessage extends Mage_Core_Helper_Abstract
{
	/**
     * Initialize gift message for entity
     *
     * @return Mage_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    public function initMessage($entity)
    {
    	$order = ($entity instanceof Mage_Sales_Model_Order)?$entity:$entity->getOrder();
    	
    	if(!$order->getGiftMessageId()){
    		return false;
    	}
        $giftMessage = Mage::helper('giftmessage/message')->getGiftMessage($order->getGiftMessageId());
        // init default values for giftmessage form
        if(!$giftMessage->getSender()) {
            $giftMessage->setSender($order->getCustomerName());
        }
        
        if(!$giftMessage->getRecipient()) {
        	if ($order->getShippingAddress()) {
                $defaultRecipient = $order->getShippingAddress()->getName();
            } else if ($order->getBillingAddress()) {
                $defaultRecipient =  $order->getBillingAddress()->getName();
            }
            $giftMessage->setRecipient($defaultRecipient);
        }
		
        return new Varien_Object($giftMessage->getData());
    }
}