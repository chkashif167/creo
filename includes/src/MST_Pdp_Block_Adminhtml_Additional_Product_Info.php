<?php
class MST_Pdp_Block_Adminhtml_Additional_Product_Info extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {

        $item = $this->getParentBlock()->getItem();
		$buyRequest = $item->getBuyRequest()->getData();
		if (isset($buyRequest['extra_options']) && $buyRequest['extra_options'] != "") {
			$itemId = $item->getId();
			$orderId = $item->getOrderId();
			$productId = $item->getProductId();
			$link = "";
			$link .= "<a class='pdp-order-item' href='#' itemid='" . $itemId . "' productid='".$productId."' orderid='".$orderId."'>". Mage::helper('pdp')->__('View Customize Design') ."</a>";
			$html = "<div id='customize-" . $itemId . "' class='view-customize-design'>";
			$html .= "<dt>".  Mage::helper('pdp')->__('Customize Design') ."</dt>";
			$html .= "<dd>" . $link . "</dd>";
			$html .= "</div>";
			return $html;
		}
    }
}