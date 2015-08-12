<?php
class MST_Pdp_Block_Email_Additional_Product_Info extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $item = $this->getParentBlock()->getItem();
        $block = $this->getLayout()->createBlock('core/template')
                ->setTemplate('pdp/email/additional/info.phtml')
                ->setData('item', $item);
        return $block->toHtml();
    }
}