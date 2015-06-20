<?php
class MST_Pdp_Block_Additional_Product_Info extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
		$item = $this->getParentBlock()->getItem();
		$buyRequest = $item->getBuyRequest()->getData();
		if (isset($buyRequest['extra_options']) && $buyRequest['extra_options'] != "") {
			$item = $this->getParentBlock()->getItem();

			$block = $this->getLayout()->createBlock('core/template')
                ->setTemplate('pdp/checkout/cart/additional/info.phtml')
                ->setData('item', $item);
			return $block->toHtml();
		}
    }
}