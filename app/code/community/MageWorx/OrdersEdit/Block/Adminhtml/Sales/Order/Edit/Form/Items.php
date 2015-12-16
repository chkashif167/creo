<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Edit_Form_Items extends Mage_Adminhtml_Block_Sales_Order_Create_Items
{
    /**
     * Preapre layout to show "edit order items" form
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $grid = $this->getLayout()->createBlock('mageworx_ordersedit/adminhtml_sales_order_edit_form_items_itemsgrid')->setTemplate('mageworx/ordersedit/edit/items/grid.phtml');
        $this->append($grid);

        return $this;
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        /** @var Mage_Sales_model_Order $order */
        $order = $this->getOrder() ? $this->getOrder() : Mage::registry('ordersedit_order');
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('mageworx_ordersedit/edit')->getQuoteByOrder($order);

        return $quote;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = $this->getChildHtml();
        $html .= '<div id="ordersedit_product_grid"></div>';

        //Configure existing order items
        $html .= <<<SCRIPT
        <script type="text/javascript">
            orderEditItems = new OrdersEditEditItems(
                    '{$this->getCurrencySymbol()}',
                    {$this->jsonEncode($this->getQuoteItemIds())}
                );
        </script>
SCRIPT;
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
        $order = $this->getOrder() ? $this->getOrder() : Mage::registry('ordersedit_order');
        $itemsIds = array();

        if ($order) {

            /** @var Mage_Sales_Model_Quote $quote */
            $quote = $this->getQuote();
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

    /**
     * Get currency symbol for order
     *
     * @return string
     * @throws Exception
     */
    protected function getCurrencySymbol()
    {
        $order = $this->getOrder() ? $this->getOrder() : Mage::registry('ordersedit_order');
        $currency = Mage::app()->getLocale()->currency($order->getOrderCurrencyCode());
        $symbol = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();

        return $symbol;
    }

    /**
     * @param $data
     * @return string
     */
    protected function jsonEncode($data)
    {
        return Zend_Json::encode($data);
    }
}