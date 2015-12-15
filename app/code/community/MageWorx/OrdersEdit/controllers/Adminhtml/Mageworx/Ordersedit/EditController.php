<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Adminhtml_Mageworx_Ordersedit_EditController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Load form to edit specific block of order
     *
     * @return $this
     */
    public function loadEditFormAction()
    {
        $blockId = $this->getRequest()->getParam('block_id');
        $orderId = $this->getRequest()->getParam('order_id');

        $block = $this->getMwEditHelper()->getBlockById($blockId);
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);

        if (!$block || !$order) {
            return $this;
        }

        $pendingChanges = $this->getMwEditHelper()->getPendingChanges($orderId);
        if (empty($pendingChanges))
        {
            $this->getMwEditHelper()->removeTempQuoteItems($order);
        }

        Mage::register('ordersedit_order', $order);

        $form = $this->getLayout()->createBlock($block['block']);
        $form->setOrder($order);

        $buttons = $this->getLayout()->createBlock('core/template')
            ->setTemplate('mageworx/ordersedit/edit/buttons.phtml');
        // Render messages block
        $errors = $this->getLayout()->createBlock('adminhtml/messages')
            ->setMessages(Mage::getSingleton('adminhtml/session')->getMessages(true))
            ->getGroupedHtml();
        $html = $errors . $form->toHtml() . $buttons->toHtml();

        $html = str_replace('var VatParameters', 'VatParameters', $html);

        $this->getResponse()->setBody($html);

        return $this;
    }

    /**
     * Load customer grid
     */
    public function customersGridAction()
    {
        $grid = $this->getLayout()->createBlock('mageworx_ordersedit/adminhtml_sales_order_edit_form_customer_grid');
        $this->getResponse()->setBody($grid->toHtml());
    }

    /**
     * load product grid
     */
    public function productGridAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);

        $grid = $this->getLayout()->createBlock('mageworx_ordersedit/adminhtml_sales_order_edit_form_items_grid');
        $grid->setData('order', $order);
        $this->getResponse()->setBody($grid->toHtml());
    }

    /**
     * Apply new customer to order (only imports data to form)
     */
    public function submitCustomerAction()
    {
        $customerId = $this->getRequest()->getParam('id');
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer')->load($customerId);

        $this->getResponse()->setBody(Zend_Json::encode($customer->getData()));
    }

    /**
     * @todo Move processing code to models
     */
    public function applyChangesAction()
    {

        try {
            $orderId = $this->getRequest()->getParam('order_id');
            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load($orderId);
            Mage::register('ordersedit_order', $order);
            /** @var Mage_Sales_Model_Quote $quote */
            $quote = Mage::getModel('mageworx_ordersedit/edit')->getQuoteByOrder($order);

            $data = $this->getRequest()->getPost();

            $pendingChanges = $this->getMwEditHelper()->addPendingChanges($orderId, $data);
            /** @var Mage_Sales_Model_Quote $quote */
            $quote = Mage::getSingleton('mageworx_ordersedit/edit_quote')->applyDataToQuote($quote, $pendingChanges);

            $order->addData($data);

            $blockId = $this->getRequest()->getParam('edited_block');
            $blockData = $this->getMwEditHelper()->getBlockById($blockId);
            $block = $this->getLayout()->createBlock($blockData['changedBlock']);

            if ($blockId == 'shipping_address') {
                $block->setAddressType('shipping');
            } elseif ($blockId == 'billing_address') {
                $block->setAddressType('billing');
            }

            $block->setQuote($quote);
            $block->setOrder($order);

            $noticeHtml = $this->getLayout()->createBlock('core/template')
                ->setTemplate('mageworx/ordersedit/changed/notice.phtml')
                ->toHtml();
            $result[$blockId] = $noticeHtml . $block->toHtml();

            // Render temp totals (preview)
            /** @var array $totals */
            $totals = $quote->getTotals();
            $tempTotalsBlock = Mage::getSingleton('core/layout')->createBlock(
                'mageworx_ordersedit/adminhtml_sales_order_totals',
                'temp_totals',
                array(
                    'totals' => $totals,
                    'order'  => $order
                )
            );
            $tempTotalsHtml = $tempTotalsBlock->toHtml();
            $result['temp_totals'] = $tempTotalsHtml;
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

        } catch (Exception $e) {
            $result = array('exception' => '1');
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }

        return $this;
    }

    public function saveOrderAction()
    {
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load($orderId);
            Mage::register('ordersedit_order', $order);
            /** @var Mage_Sales_Model_Quote $quote */
            $quote = Mage::getModel('mageworx_ordersedit/edit')->getQuoteByOrder($order);

            $pendingChanges = $this->getMwEditHelper()->getPendingChanges($orderId);

            if ($pendingChanges) {

                $origOrder = clone $order;

                Mage::getSingleton('mageworx_ordersedit/edit_quote')->applyDataToQuote($quote, $pendingChanges);
                Mage::getSingleton('mageworx_ordersedit/edit')->saveOrder($quote, $order, $pendingChanges);
                Mage::getSingleton('mageworx_ordersedit/edit_quote')->saveTemporaryItems($quote, 0, false); // Drop is_temporary flag from items

                $invoices = $order->getInvoiceCollection();
                if ($order->getGrandTotal() > ($origOrder->getGrandTotal() - $origOrder->getBaseTotalRefunded()) && count($invoices)) { // Create invoice if needed
                    Mage::getSingleton('mageworx_ordersedit/edit_invoice')->invoiceChanges($origOrder, $order, $pendingChanges);
                } elseif ($order->getGrandTotal() < ($origOrder->getGrandTotal() - $origOrder->getBaseTotalRefunded()) && count($invoices)) { // Create refund if needed
                    Mage::getSingleton('mageworx_ordersedit/edit_creditmemo')->refundChanges($origOrder, $order, $pendingChanges);
                }

                $this->getMwEditHelper()->resetPendingChanges($orderId);
            }

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order changes have been saved'));

        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')
                ->addError($this->__('An error occured while saving the order' . $e->getMessage()));
        }

        $this->_redirectReferer();
    }

    /**
     * Unset all temporary quote data
     */
    public function cancelChangesAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);
        $this->getMwEditHelper()->removeTempQuoteItems($order);
        $this->getMwEditHelper()->resetPendingChanges($orderId);
        Mage::getSingleton('adminhtml/session_quote')->unsetData();
        Mage::getSingleton('adminhtml/session_quote')
            ->setData('base_shipping_custom_price', $order->getBaseShippingAmount());
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order changes have been canceled'));

        $this->_redirectReferer();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/sales/mageworx_ordersedit');
    }

    /**
     * @return MageWorx_OrdersEdit_Helper_Edit
     */
    protected function getMwEditHelper()
    {
        return Mage::helper('mageworx_ordersedit/edit');
    }
}