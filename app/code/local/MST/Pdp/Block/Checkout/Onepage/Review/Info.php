<?php
class MST_Pdp_Block_Checkout_Onepage_Review_Info extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $item = $this->getParentBlock()->getItem();
        $block = $this->getLayout()->createBlock('core/template')
                ->setTemplate('pdp/checkout/onepage/review/info.phtml')
                ->setData('item', $item);
        return $block->toHtml();
    }
}