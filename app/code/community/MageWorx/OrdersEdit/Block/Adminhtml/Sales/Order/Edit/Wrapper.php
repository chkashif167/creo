<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Edit_Wrapper extends Mage_Adminhtml_Block_Template
{
    /**
     * Get blocks of order which can be edited (JSON)
     *
     * @return string
     */
    public function getBlocksJson()
    {
        $blocks = Mage::helper('mageworx_ordersedit/edit')->getAvailableBlocks();
        return Zend_Json::encode($blocks);
    }

    /**
     * Get currency symbol for order
     *
     * @return string
     * @throws Exception
     */
    public function getCurrencySymbol()
    {
        $orderId = $this->getRequest()->getParam('order_id', false);
        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order || !$order->getId()) {
            return '';
        }

        $currency = Mage::app()->getLocale()->currency($order->getOrderCurrencyCode());
        $symbol = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();

        return $symbol;
    }

    /**
     * Preapre html for output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $html .= $this->getLayout()->createBlock('adminhtml/catalog_product_composite_configure')->toHtml();
        return $html;
    }

    /**
     * Get quote items ids as array for orderEditItems init. (JS)
     *
     * @return array
     */
    public function getQuoteItemIds()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $this->getLayout()->getBlock('order_items')->getOrder();
        $itemsIds = array();

        if ($order) {

            /** @var Mage_Sales_Model_Quote $quote */
            $quote = Mage::getSingleton('mageworx_ordersedit/edit')->getQuoteByOrder($order);
            $this->setQuote($quote);
            $items = $quote->getAllVisibleItems();

            /** @var Mage_Sales_Model_Quote_item $item */
            foreach ($items as $item) {
                $id = $item->getId();
                if ($id) {
                    $itemsIds[$id] = $item->getBuyRequest()->toArray();
                }
            }
        }

        return $itemsIds;
    }

}