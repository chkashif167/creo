<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Changed_Items extends Mage_Adminhtml_Block_Sales_Order_View_Items
{
    /** @var string  */
    protected $_template = 'mageworx/ordersedit/changed/items.phtml';

    public function _construct()
    {
        parent::_construct();
        // Data from layout
        $this->addItemRender('default', 'adminhtml/sales_order_view_items_renderer_default', 'sales/order/view/items/renderer/default.phtml');
        $this->addItemRender('bundle', 'bundle/adminhtml_sales_order_view_items_renderer', 'bundle/sales/order/view/items/renderer.phtml');
        $this->addItemRender('downloadable', 'adminhtml/sales_order_view_items_renderer_default', 'downloadable/sales/order/view/items/renderer/downloadable.phtml');

        $this->addColumnRender('qty', 'adminhtml/sales_items_column_qty', 'sales/items/column/qty.phtml');
        $this->addColumnRender('name', 'adminhtml/sales_items_column_name', 'mageworx/ordersedit/sales-items-column-name.phtml');
        $this->addColumnRender('name', 'adminhtml/sales_items_column_name_grouped', 'mageworx/ordersedit/sales-items-column-name.phtml', 'grouped');
        $this->addColumnRender('downloadable', 'downloadable/adminhtml_sales_items_column_downloadable_name', 'downloadable/sales/items/column/downloadable/name.phtml');

    }

    public function _beforeToHtml()
    {
        $this->setParentBlock($this->getLayout()->createBlock('core/template', '', array('order'=>$this->getOrder())));
        parent::_beforeToHtml();
        //@todo: append <block type="core/text_list" name="order_item_extra_info" />
    }

    /**
     * Retrieve order items collection
     *
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    public function getItemsCollection()
    {

        $newItemArray = array();
        $newId = 1;

        /** @var MageWorx_OrdersEdit_Model_Edit_Quote_Convert $converter */
        $converter = Mage::getModel('mageworx_ordersedit/edit_quote_convert');
        $order = $this->getOrder();
        $quoteItems = $this->getQuote()->getAllVisibleItems();
        $orderItemsCollection = Mage::getResourceModel('sales/order_item_collection')->addIdFilter(-1);
        $changes = Mage::helper('mageworx_ordersedit/edit')->getPendingChanges($order->getId());
        if (isset($changes['quote_items'])) {
            $itemChanges = $changes['quote_items'];
        }

        // Sort items
        foreach ($quoteItems as $quoteItem)
        {
            // Validate items:
            // Removed item
            if (isset($itemChanges[$quoteItem->getItemId()]['action']) && $itemChanges[$quoteItem->getItemId()]['action'] == 'remove') {
                continue;
            }

            // 0 qty item
            if (isset($itemChanges[$quoteItem->getItemId()]['qty']) && $itemChanges[$quoteItem->getItemId()]['qty'] < 0.001) {
                continue;
            }

            // Child item
            if ($quoteItem->getParentItem()) {
                continue;
            }

            $newId = $newId+1;
            $newItem = $converter->itemToOrderItem($quoteItem);
            $newItem->setId($quoteItem->getId() ? $quoteItem->getId() : $newId)
                ->setOrder($order);

            if (count($quoteItem->getChildren()) > 0)
            {
                $childArray = array();
                foreach ($quoteItem->getChildren() as $quoteItemChild)
                {
                    $newId = $newId+1;
                    $newChild = $converter->itemToOrderItem($quoteItemChild)
                        ->setId($quoteItemChild->getId() ? $quoteItemChild->getId() : $newId)
                        ->setOrder($order)
                        ->setParentItemId($newItem->getId())
                        ->setParentItem($newItem);
                    $childArray[] = $newChild;
                }

                $newItem->setChildrenItems(
                    array_merge(
                        $childArray,                    // current item
                        $newItem->getChildrenItems()    // all items parent has before
                    )
                );
            }

            // add current item to array
            $newItemArray[] = $newItem;
        }

        foreach ($newItemArray as $orderItem)
        {
            try {
                $orderItemsCollection->addItem($orderItem);
            } catch (Exception $e) {
                continue;
            }
        }

        return $orderItemsCollection;
    }

}