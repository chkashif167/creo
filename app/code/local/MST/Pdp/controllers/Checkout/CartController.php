<?php
require_once Mage::getBaseDir('app').DS.'code'.DS.'core'.DS.'Mage'.DS.'Checkout'.DS.'controllers'.DS.'CartController.php';
class MST_PDP_Checkout_CartController extends Mage_Checkout_CartController
{
    public function updateItemOptionsAction()
    {
        $cart   = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();
        if (isset($params['extra_options']) && $params['extra_options'] != "") {
            $quoteItem = $cart->getQuote()->getItemById($id);
            try {
                if($quoteItem->getId()) {
                    $quoteItem->delete();
                    $this->_forward('add');
                }
            } catch (Exception $e) {

            }
        } else {
            parent::updateItemOptionsAction();
        }

    }
}
