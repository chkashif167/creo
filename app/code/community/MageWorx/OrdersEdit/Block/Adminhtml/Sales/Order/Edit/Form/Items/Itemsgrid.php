<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Edit_Form_Items_Itemsgrid extends Mage_Adminhtml_Block_Sales_Order_Create_Items_Grid
{
    protected $_subtotal = null;
    protected $_discount = null;
    protected $_items = array();

    public function _construct()
    {
        parent::_construct();
        $order = $this->getOrder() ? $this->getOrder() : Mage::registry('ordersedit_order');
        $storeId = $order->getStoreId();
        $store = Mage::getModel('core/store')->load($storeId);
        Mage::app()->setCurrentStore($store);
    }
    /**
     * Get button to configure product
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return string
     */
    public function getConfigureButtonHtml($item)
    {
        $product = $item->getProduct();

        $options = array('label' => Mage::helper('sales')->__('Configure'));
        if ($product->canConfigure()) {
            $options['onclick'] = sprintf('orderEditItems.showQuoteItemConfiguration(%s)', $item->getId());
        } else {
            $options['class'] = ' disabled';
            $options['title'] = Mage::helper('sales')->__('This product does not have any configurable options');
        }

        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData($options)
            ->toHtml();
    }

    /**
     * @return null|float
     */
    public function getSubtotal()
    {
        if (is_null($this->_subtotal)) {
            foreach ($this->getItems() as $item) {
                $this->_subtotal += $item->getRowTotal();
            }
        }

        return $this->_subtotal;
    }

    /**
     * @return null|float
     */
    public function getDiscountAmount()
    {
        if (is_null($this->_discount)) {
            foreach ($this->getItems() as $item) {
                if (count($item->getChildren()) > 0) {
                    foreach ($item->getChildren() as $childItem) {
                        $this->_discount += $childItem->getDiscountAmount();
                    }
                }
                $this->_discount += $item->getDiscountAmount();
            }
        }

        return $this->_discount;
    }

    /**
     * Returns the items
     *
     * @return array
     */
    public function getItems()
    {
        if (!empty($this->_items)) {
            return $this->_items;
        }

        $items = $this->getParentBlock()->getItems();
        $oldSuperMode = $this->getQuote()->getIsSuperMode();
        $this->getQuote()->setIsSuperMode(false);

        foreach ($items as $item) {
            if (!$item->getId()) {
                $item->setId($item->getProductId());
            }
            // To dispatch inventory event sales_quote_item_qty_set_after, set item qty
            $qty = floatval($item->getRowTotal()) ? $item->getQty() : 0;
            $item->setQty($qty);
            $stockItem = $item->getProduct()->getStockItem();
            if ($stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
                if ($item->getProduct()->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED) {
                    $item->setMessage(Mage::helper('adminhtml')->__('This product is currently disabled.'));
                    $item->setHasError(true);
                }
            }
        }
        $this->getQuote()->setIsSuperMode($oldSuperMode);

        foreach ($items as $key => $item) {
            /** @var MageWorx_OrdersEdit_Model_Edit_Quote $model */
            $model = Mage::getSingleton('mageworx_ordersedit/edit_quote');
            if ($model->clearQuoteItems($item, true)) {
                unset($items[$key]);
            }
        }

        $this->_items = $items;
        return $items;
    }

}