<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_Observer
{

    /**
     * Archive orders by cron
     *
     * Cron job: mageworx_ordersgrid_archive
     * Schedule: 0 0 * * *
     *
     * @param $schedule
     */
    public function scheduledArchiveOrders($schedule)
    {
        $helper = $this->getMwHelper();
        $days = $helper->getDaysBeforeOrderGetArchived();
        if (!$helper->isEnabled() || $days == 0) {
            return;
        }
        $archiveOrdersStatus = $helper->getArchiveOrderStatuses();
        /** @var MageWorx_OrdersGrid_Model_Resource_Order_Collection $orders */
        $orders = Mage::getResourceModel('mageworx_ordersgrid/order_collection')->setFilterOrdersNoGroup($days)->addFieldToFilter('status', array('in' => $archiveOrdersStatus));
        $orderIds = array();
        foreach ($orders as $ord) {
            $orderIds[] = $ord->getEntityId();
        }

        // to archive
        $helper->addToOrderGroup($orderIds, 1);
    }

    /**
     * Extends mass actions at orders grid
     *
     * Event: core_block_abstract_to_html_before
     * Observer Name: mageworx_add_mass_actions
     *
     * @param $observer
     */
    public function addMassActionToSalesOrdersGrid($observer)
    {
        /** @var Mage_Core_Block_Abstract $block */
        $block = $observer->getBlock();
        if($block->getType() == 'adminhtml/widget_grid_massaction' && ($block->getParentBlock() instanceof Mage_Adminhtml_Block_Sales_Order_Grid))
        {
            /** @var Mage_Adminhtml_Block_Widget_Grid_Massaction $block */
            if ($this->getMwHelper()->isEnabled()) {

                if ($this->getMwHelper()->isEnableInvoiceOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/mageworx_ordersgrid/actions/invoice')) {
                    $block->addItem('invoice_order', array(
                        'label' => $this->getMwHelper()->__('Invoice'),
                        'url' => $this->getUrl('adminhtml/mageworx_ordersgrid/massInvoice'),
                    ));
                }

                if ($this->getMwHelper()->isEnableShipOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/mageworx_ordersgrid/actions/ship')) {
                    $block->addItem('ship_order', array(
                        'label' => $this->getMwHelper()->__('Ship'),
                        'url' => $this->getUrl('adminhtml/mageworx_ordersgrid/massShip'),
                    ));
                }

                if ($this->getMwHelper()->isEnableInvoiceOrders() && $this->getMwHelper()->isEnableShipOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/mageworx_ordersgrid/actions/invoice_and_ship')) {
                    $block->addItem('invoice_and_ship_order', array(
                        'label' => $this->getMwHelper()->__('Invoice+Ship'),
                        'url' => $this->getUrl('adminhtml/mageworx_ordersgrid/massInvoiceAndShip'),
                    ));
                }

                if ($this->getMwHelper()->isEnableInvoiceOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/mageworx_ordersgrid/actions/invoice')) {
                    $block->addItem('invoice_and_print', array(
                        'label' => $this->getMwHelper()->__('Invoice+Print'),
                        'url' => $this->getUrl('adminhtml/mageworx_ordersgrid/massInvoiceAndPrint'),
                    ));
                }

                if ($this->getMwHelper()->isEnableArchiveOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/mageworx_ordersgrid/actions/archive')) {
                    $block->addItem('archive_order', array(
                        'label' => $this->getMwHelper()->__('Archive'),
                        'url' => $this->getUrl('adminhtml/mageworx_ordersgrid/massArchive'),
                    ));
                }


                if ($this->getMwHelper()->isEnableDeleteOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/mageworx_ordersgrid/actions/delete')) {
                    $block->addItem('delete_order', array(
                        'label' => $this->getMwHelper()->__('Delete'),
                        'url' => $this->getUrl('adminhtml/mageworx_ordersgrid/massDelete'),
                    ));
                }

                if ($this->getMwHelper()->isEnableDeleteOrdersCompletely() && Mage::getSingleton('admin/session')->isAllowed('sales/mageworx_ordersgrid/actions/delete_completely')) {
                    $block->addItem('delete_order_completely', array(
                        'label' => $this->getMwHelper()->__('Delete Completely'),
                        'url' => $this->getUrl('adminhtml/mageworx_ordersgrid/massDeleteCompletely'),
                    ));
                }


                if (($this->getMwHelper()->isEnableArchiveOrders() || $this->getMwHelper()->isEnableDeleteOrders()) && (Mage::getSingleton('admin/session')->isAllowed('sales/mageworx_ordersgrid/actions/archive') || Mage::getSingleton('admin/session')->isAllowed('sales/mageworx_ordersgrid/actions/delete'))) {
                    $block->addItem('restore_order', array(
                        'label' => $this->getMwHelper()->__('Restore'),
                        'url' => $this->getUrl('adminhtml/mageworx_ordersgrid/massRestore'),
                    ));
                }
            }
        }

        return;
    }

    /**
     * Update columns of sales order grid
     *
     * Event: core_layout_block_create_after
     * Observer Name: mageworx_add_custom_columns
     *
     * @param $observer
     */
    public function addCustomColumnsToSalesOrdersGrid($observer)
    {
        $helper = $this->getMwHelper();
        if (!$helper->isEnabled()) {
            return;
        }
        /** @var Mage_Core_Block_Abstract $block */
        $block = $observer->getBlock();
        if($block->getType() == 'adminhtml/widget_grid_massaction') {
            /** @var Mage_Adminhtml_Block_Sales_Order_Grid $block */
            $block = $block->getLayout()->getBlock('sales_order.grid');
            if (!$block) {
                return;
            }
            $allColumns = $helper->getAllGridColumns();
            $sortedColumns = $helper->getGridColumnsSortOrder();
            if (!empty($sortedColumns)) {
                $allColumns = array_flip($sortedColumns);
            }
            $listColumns = $helper->getGridColumns();
            foreach ($allColumns as $position => $column) {
                switch ($column) {

                    /** ==================== Regular Columns ==================== */
                    case 'real_order_id':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    case 'store_id':
                        if (!Mage::app()->isSingleStoreMode()) {
                            if (!in_array($column, $listColumns)) {
                                $block->removeColumn($column);
                            } else {
                                $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                            }
                            break;
                        }
                        break;

                    case 'created_at':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    case 'billing_name':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    case 'shipping_name':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    case 'base_grand_total':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    case 'grand_total':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    case 'status':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    case 'action':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    /** ==================== Custom Columns ==================== */
                    case 'product_names':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('product_names', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_products',
                                'header' => $helper->__('Product Name(s)') . (!strpos(Mage::app()->getRequest()->getRequestString(), '/exportCsv/') ? '' : ''),
                                'index' => 'product_names',
                                'column_css_class' => 'mw-orders-grid-product_names'
                            ));
                        }
                        break;

                    case 'product_skus':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('product_skus', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_products',
                                'header' => $helper->__('SKU(s)'),
                                'index' => 'skus'
                            ));
                        }
                        break;

                    case 'product_options':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('product_options', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_products',
                                'header' => $helper->__('Product Option(s)'),
                                'index' => 'product_options',
                                'filter' => false,
                                'sortable' => false
                            ));
                        }
                        break;

                    case 'customer_email':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('customer_email', array(
                                'type' => 'text',
                                'header' => $helper->__('Customer Email'),
                                'index' => 'customer_email'
                            ));
                        }
                        break;

                    case 'customer_group':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('customer_group', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_registry',
                                'type' => 'options',
                                'options' => $helper->getCustomerGroups(),
                                'header' => $helper->__('Customer Group'),
                                'index' => 'customer_group_id',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'payment_method':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('payment_method', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_registry',
                                'type' => 'options',
                                'options' => $helper->getAllPaymentMethods(),
                                'header' => $helper->__('Payment Method'),
                                'index' => 'method',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'base_total_refunded':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('base_total_refunded', array(
                                'type' => 'currency',
                                'currency' => 'base_currency_code',
                                'header' => $helper->__('Total Refunded (Base)'),
                                'index' => 'base_total_refunded',
                                'total' => 'sum'
                            ));
                        }
                        break;

                    case 'total_refunded':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('total_refunded', array(
                                'type' => 'currency',
                                'currency' => 'order_currency_code',
                                'header' => $helper->__('Total Refunded (Purchased)'),
                                'index' => 'total_refunded',
                                'total' => 'sum'
                            ));
                        }
                        break;

                    case 'shipping_method':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_method', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_registry',
                                'type' => 'options',
                                'options' => $helper->getAllShippingMethods(),
                                'filter' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_filter_shipping',
                                'header' => $helper->__('Shipping Method'),
                                'index' => 'shipping_method',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'tracking_number':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('tracking_number', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_street',
                                'type' => 'text',
                                'header' => $helper->__('Tracking Number'),
                                'index' => 'tracking_number'
                            ));
                        }
                        break;

                    case 'shipped':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipped', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_registry',
                                'type' => 'options',
                                'options' => $helper->getShippedStatuses(),
                                'header' => $helper->__('Shipped'),
                                'index' => 'shipped',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'order_group':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('order_group', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_registry',
                                'type' => 'options',
                                'options' => $helper->getOrderGroups(),
                                'header' => $helper->__('Group'),
                                'index' => 'order_group_id',
                                'align' => 'center',
                            ));
                        }
                        break;

                    case 'qnty':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('qnty', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_qnty',
                                'filter' => false,
                                'sortable' => false,
                                'header' => $helper->__('Qnty'),
                                'index' => 'total_qty',
                            ));
                        }
                        break;

                    case 'weight':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('weight', array(
                                'type' => 'number',
                                'header' => $helper->__('Weight'),
                                'index' => 'weight',
                            ));
                        }
                        break;

                    case 'base_tax_amount':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('base_tax_amount', array(
                                'type' => 'currency',
                                'currency' => 'base_currency_code',
                                'header' => $helper->__('Tax Amount (Base)'),
                                'index' => 'base_tax_amount'
                            ));
                        }
                        break;

                    case 'tax_amount':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('tax_amount', array(
                                'type' => 'currency',
                                'currency' => 'order_currency_code',
                                'header' => $helper->__('Tax Amount (Purchased)'),
                                'index' => 'tax_amount'
                            ));
                        }
                        break;

                    case 'shipping_amount':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_amount', array(
                                'type' => 'currency',
                                'currency' => 'order_currency_code',
                                'header' => $helper->__('Shipping Amount (Purchased)'),
                                'index' => 'shipping_amount'
                            ));
                        }
                        break;

                    case 'base_shipping_amount':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('base_shipping_amount', array(
                                'type' => 'currency',
                                'currency' => 'base_currency_code',
                                'header' => $helper->__('Shipping Amount (Base)'),
                                'index' => 'base_shipping_amount'
                            ));
                        }
                        break;

                    case 'subtotal':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('subtotal', array(
                                'type' => 'currency',
                                'currency' => 'order_currency_code',
                                'header' => $helper->__('Subtotal (Purchased)'),
                                'index' => 'subtotal'
                            ));
                        }
                        break;

                    case 'base_subtotal':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('base_subtotal', array(
                                'type' => 'currency',
                                'currency' => 'base_currency_code',
                                'header' => $helper->__('Subtotal (Base)'),
                                'index' => 'base_subtotal'
                            ));
                        }
                        break;

                    case 'base_discount_amount':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('base_discount_amount', array(
                                'type' => 'currency',
                                'currency' => 'base_currency_code',
                                'header' => $helper->__('Discount (Base)'),
                                'index' => 'base_discount_amount'
                            ));
                        }
                        break;

                    case 'discount_amount':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('discount_amount', array(
                                'type' => 'currency',
                                'currency' => 'order_currency_code',
                                'header' => $helper->__('Discount (Purchased)'),
                                'index' => 'discount_amount'
                            ));
                        }
                        break;

                    case 'base_internal_credit':
                        if (in_array($column, $listColumns)) {
                            if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
                                $block->addColumn('base_internal_credit', array(
                                    'type' => 'currency',
                                    'currency' => 'base_currency_code',
                                    'header' => $helper->__('Internal Credit (Base)'),
                                    'index' => 'base_customer_credit_amount'
                                ));
                            }
                        }
                        break;
                    case 'internal_credit':
                        if (in_array($column, $listColumns)) {
                            if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
                                $block->addColumn('internal_credit', array(
                                    'type' => 'currency',
                                    'currency' => 'order_currency_code',
                                    'header' => $helper->__('Internal Credit (Purchased)'),
                                    'index' => 'customer_credit_amount'
                                ));
                            }
                        }
                        break;

                    case 'billing_company':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('billing_company', array(
                                'type' => 'text',
                                'header' => $helper->__('Bill to Company'),
                                'index' => 'billing_company',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'shipping_company':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_company', array(
                                'type' => 'text',
                                'header' => $helper->__('Ship to Company'),
                                'index' => 'shipping_company',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'billing_street':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('billing_street', array(
                                'type' => 'text',
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_street',
                                'header' => $helper->__('Bill to Street'),
                                'index' => 'billing_street',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'shipping_street':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_street', array(
                                'type' => 'text',
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_street',
                                'header' => $helper->__('Ship to Street'),
                                'index' => 'shipping_street',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'billing_city':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('billing_city', array(
                                'type' => 'text',
                                'header' => $helper->__('Bill to City'),
                                'index' => 'billing_city',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'shipping_city':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_city', array(
                                'type' => 'text',
                                'header' => $helper->__('Ship to City'),
                                'index' => 'shipping_city',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'billing_region':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('billing_region', array(
                                'type' => 'text',
                                'header' => $helper->__('Bill to State'),
                                'index' => 'billing_region',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'shipping_region':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_region', array(
                                'type' => 'text',
                                'header' => $helper->__('Ship to State'),
                                'index' => 'shipping_region',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'billing_country':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('billing_country', array(
                                'type' => 'options',
                                'options' => $helper->getCountryNames(),
                                'header' => $helper->__('Bill to Country'),
                                'index' => 'billing_country_id',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'shipping_country':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_country', array(
                                'type' => 'options',
                                'header' => $helper->__('Ship to Country'),
                                'options' => $helper->getCountryNames(),
                                'index' => 'shipping_country_id',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'billing_postcode':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('billing_postcode', array(
                                'type' => 'text',
                                'header' => $helper->__('Billing Postcode'),
                                'index' => 'billing_postcode',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'shipping_postcode':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_postcode', array(
                                'type' => 'text',
                                'header' => $helper->__('Shipping Postcode'),
                                'index' => 'shipping_postcode',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'billing_telephone':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('billing_telephone', array(
                                'type' => 'text',
                                'header' => $helper->__('Billing Telephone'),
                                'index' => 'billing_telephone',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'shipping_telephone':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_telephone', array(
                                'type' => 'text',
                                'header' => $helper->__('Shipping Telephone'),
                                'index' => 'shipping_telephone',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'coupon_code':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('coupon_code', array(
                                'type' => 'text',
                                'header' => $helper->__('Coupon Code'),
                                'align' => 'center',
                                'index' => 'coupon_code'
                            ));
                        }
                        break;

                    case 'is_edited':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('is_edited', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_registry',
                                'type' => 'options',
                                'options' => $helper->getEditedStatuses(),
                                'header' => $helper->__('Edited'),
                                'index' => 'is_edited',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'order_comment':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('order_comment', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_comments',
                                'header' => $helper->__('Order Comment(s)'),
                                'index' => 'order_comment'
                            ));
                        }
                        break;
                }
            }
        }

        return;
    }

    /**
     * Add custom columns in sales order grid collection
     *
     * Event: sales_order_grid_collection_load_before
     * Observer Name: mageworx_add_custom_columns_select
     *
     * @param $observer
     * @return void
     */
    public function addCustomColumnsSelect($observer)
    {
        $helper = $this->getMwHelper();
        if ($helper->isEnabled()) {
            Varien_Profiler::start('mw_addCustomColumnsSelect');
            /** @var Mage_Sales_Model_Resource_Order_Grid_Collection $orderCollection */
            $orderGridCollection = $observer->getOrderGridCollection();
            /** @var MageWorx_OrdersGrid_Model_Grid $model */
            $model = Mage::getModel('mageworx_ordersgrid/grid');
            if (Mage::app()->getRequest()->getControllerName() == 'customer') {
                if (Mage::app()->getRequest()->getActionName() != 'orders') {
                    return;
                }
                $listColumns = $helper->getCustomerGridColumns();
                $model->modifyCustomerOrdersGridCollection($orderGridCollection, $listColumns);
            } else {
                $listColumns = $helper->getGridColumns();
                $model->modifyOrdersGridCollection($orderGridCollection, $listColumns);
            }
            Varien_Profiler::stop('mw_addCustomColumnsSelect');
        }

        return;
    }

    /**
     * Update columns of sales order grid for customer (customer tab: orders)
     *
     * Event: core_layout_block_create_after
     * Observer Name: mageworx_add_custom_columns_for_customer
     *
     * @param $observer
     */
    public function addCustomColumnsToCustomerOrdersGrid($observer)
    {
        $helper = $this->getMwHelper();
        if (!$helper->isEnabled()) {
            return;
        }
        /** @var Mage_Core_Block_Abstract $block */
        $block = $observer->getBlock();
        if($block->getType() == 'adminhtml/widget_grid_massaction') {
            /** @var Mage_Adminhtml_Block_Customer_Edit_Tab_Orders $block */
            $block = $block->getLayout()->getBlock('adminhtml.customer.edit.tab.orders');
            if (!$block) {
                return;
            }
            $allColumns = $helper->getAllCustomerGridColumns();
            $sortedColumns = $helper->getCustomerGridColumnsSortOrder();
            if (!empty($sortedColumns)) {
                $allColumns = array_flip($sortedColumns);
            }
            $listColumns = $helper->getCustomerGridColumns();
            foreach ($allColumns as $position => $column) {
                switch ($column) {

                    /** ======================== Regular Fields ======================== */
                    case 'increment_id':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    case 'created_at':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    case 'billing_name':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    case 'shipping_name':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    case 'grand_total':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    case 'store_id':
                        if (!Mage::app()->isSingleStoreMode()) {
                            if (!in_array($column, $listColumns)) {
                                $block->removeColumn($column);
                            } else {
                                $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                            }
                        }
                        break;

                    case 'action':
                        if (!in_array($column, $listColumns)) {
                            $block->removeColumn($column);
                        } else {
                            $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                        }
                        break;

                    /** ======================== Additional Fields ======================== */

                    case 'product_names':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('product_names', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_products',
                                'header' => $helper->__('Product Name(s)'),
                                'index' => 'product_names',
                                'column_css_class' => 'mw-orders-grid-product_names'
                            ));
                        }
                        break;

                    case 'product_skus':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('product_skus', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_products',
                                'header' => $helper->__('SKU(s)'),
                                'index' => 'skus'
                            ));
                        }
                        break;

                    case 'product_options':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('product_options', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_products',
                                'header' => $helper->__('Product Option(s)'),
                                'index' => 'product_options',
                                'filter' => false,
                                'sortable' => false
                            ));
                        }
                        break;

                    case 'customer_email':
                        $block->addColumn('customer_email', array(
                            'type' => 'text',
                            'header' => $helper->__('Customer Email'),
                            'index' => 'customer_email'
                        ));
                        break;


                    case 'customer_group':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('customer_group', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_registry',
                                'type' => 'options',
                                'options' => $helper->getCustomerGroups(),
                                'header' => $helper->__('Customer Group'),
                                'index' => 'customer_group_id',
                                'align' => 'center'
                            ));
                        }
                        break;


                    case 'payment_method':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('payment_method', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_registry',
                                'type' => 'options',
                                'options' => $helper->getAllPaymentMethods(),
                                'header' => $helper->__('Payment Method'),
                                'index' => 'method',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'base_total_refunded':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('base_total_refunded', array(
                                'type' => 'currency',
                                'currency' => 'base_currency_code',
                                'header' => $helper->__('Total Refunded (Base)'),
                                'index' => 'base_total_refunded',
                                'total' => 'sum'
                            ));
                        }
                        break;

                    case 'total_refunded':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('total_refunded', array(
                                'type' => 'currency',
                                'currency' => 'order_currency_code',
                                'header' => $helper->__('Total Refunded (Purchased)'),
                                'index' => 'total_refunded',
                                'total' => 'sum'
                            ));
                        }
                        break;

                    case 'shipping_method':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_method', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_registry',
                                'type' => 'options',
                                'options' => $helper->getAllShippingMethods(),
                                'filter' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_filter_shipping',
                                'header' => $helper->__('Shipping Method'),
                                'index' => 'shipping_method',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'tracking_number':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('tracking_number', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_street',
                                'type' => 'text',
                                'header' => $helper->__('Tracking Number'),
                                'index' => 'tracking_number'
                            ));
                        }
                        break;

                    case 'shipped':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipped', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_registry',
                                'type' => 'options',
                                'options' => $helper->getShippedStatuses(),
                                'header' => $helper->__('Shipped'),
                                'index' => 'shipped',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'order_group':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('order_group', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_registry',
                                'type' => 'options',
                                'options' => $helper->getOrderGroups(),
                                'header' => $helper->__('Group'),
                                'index' => 'order_group_id',
                                'align' => 'center',
                            ));
                        }
                        break;

                    case 'qnty':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('qnty', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_qnty',
                                'filter' => false,
                                'sortable' => false,
                                'header' => $helper->__('Qnty'),
                                'index' => 'total_qty',
                            ));
                        }
                        break;

                    case 'weight':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('weight', array(
                                'type' => 'number',
                                'header' => $helper->__('Weight'),
                                'index' => 'weight',
                            ));
                        }
                        break;

                    case 'base_tax_amount':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('base_tax_amount', array(
                                'type' => 'currency',
                                'currency' => 'base_currency_code',
                                'header' => $helper->__('Tax Amount (Base)'),
                                'index' => 'base_tax_amount'
                            ));
                        }
                        break;

                    case 'tax_amount':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('tax_amount', array(
                                'type' => 'currency',
                                'currency' => 'order_currency_code',
                                'header' => $helper->__('Tax Amount (Purchased)'),
                                'index' => 'tax_amount'
                            ));
                        }
                        break;

                    case 'base_discount_amount':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('base_discount_amount', array(
                                'type' => 'currency',
                                'currency' => 'base_currency_code',
                                'header' => $helper->__('Discount (Base)'),
                                'index' => 'base_discount_amount'
                            ));
                        }
                        break;

                    case 'discount_amount':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('discount_amount', array(
                                'type' => 'currency',
                                'currency' => 'order_currency_code',
                                'header' => $helper->__('Discount (Purchased)'),
                                'index' => 'discount_amount'
                            ));
                        }
                        break;

                    case 'base_internal_credit':
                        if (in_array($column, $listColumns)) {
                            if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
                                $block->addColumn('base_internal_credit', array(
                                    'type' => 'currency',
                                    'currency' => 'base_currency_code',
                                    'header' => $helper->__('Internal Credit (Base)'),
                                    'index' => 'base_customer_credit_amount'
                                ));
                            }
                        }
                        break;

                    case 'internal_credit':
                        if (in_array($column, $listColumns)) {
                            if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
                                $block->addColumn('internal_credit', array(
                                    'type' => 'currency',
                                    'currency' => 'order_currency_code',
                                    'header' => $helper->__('Internal Credit (Purchased)'),
                                    'index' => 'customer_credit_amount'
                                ));
                            }
                        }
                        break;

                    case 'billing_company':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('billing_company', array(
                                'type' => 'text',
                                'header' => $helper->__('Bill to Company'),
                                'index' => 'billing_company',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'shipping_company':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_company', array(
                                'type' => 'text',
                                'header' => $helper->__('Ship to Company'),
                                'index' => 'shipping_company',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'billing_city':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('billing_city', array(
                                'type' => 'text',
                                'header' => $helper->__('Bill to City'),
                                'index' => 'billing_city',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'shipping_city':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_city', array(
                                'type' => 'text',
                                'header' => $helper->__('Ship to City'),
                                'index' => 'shipping_city',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'billing_postcode':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('billing_postcode', array(
                                'type' => 'text',
                                'header' => $helper->__('Billing Postcode'),
                                'index' => 'billing_postcode',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'shipping_postcode':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('shipping_postcode', array(
                                'type' => 'text',
                                'header' => $helper->__('Shipping Postcode'),
                                'index' => 'shipping_postcode',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'coupon_code':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('coupon_code', array(
                                'type' => 'text',
                                'header' => $helper->__('Coupon Code'),
                                'align' => 'center',
                                'index' => 'coupon_code'
                            ));
                        }
                        break;

                    case 'is_edited':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('is_edited', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_registry',
                                'type' => 'options',
                                'options' => $helper->getEditedStatuses(),
                                'header' => $helper->__('Edited'),
                                'index' => 'is_edited',
                                'align' => 'center'
                            ));
                        }
                        break;

                    case 'status':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('status', array(
                                'header' => Mage::helper('sales')->__('Status'),
                                'index' => 'status',
                                'type' => 'options',
                                'width' => '70px',
                                'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
                            ));
                        }
                        break;

                    case 'order_comment':
                        if (in_array($column, $listColumns)) {
                            $block->addColumn('order_comment', array(
                                'renderer' => 'mageworx_ordersgrid/adminhtml_sales_order_grid_renderer_comments',
                                'header' => $helper->__('Order Comment(s)'),
                                'index' => 'order_comment'
                            ));
                        }
                        break;
                }
            }
        }

        return;
    }

    /**
     * Hide deleted (group <> 2) orders on frontend
     *
     * Event: sales_order_collection_load_before
     * Observer Name: mageworx_hide_deleted_orders
     *
     * @param $observer
     */
    public function hideDeletedOrders($observer)
    {
        $helper = $this->getMwHelper();
        if ($helper->isEnabled() && $helper->isHideDeletedOrdersForCustomers()) {
            /** @var Mage_Sales_Model_Resource_Order_Collection $orderCollection */
            $orderCollection = $observer->getOrderCollection();
            $orderCollection->addFieldToFilter('order_group_id', array('neq' => '2'));
        }
    }

    /**
     * @param int $position
     * @param array $allColumns
     * @param Mage_Adminhtml_Block_Sales_Order_Grid $block
     * @param string $column
     *
     * @return void
     */
    protected function addColumnBySortPosition($position, $allColumns, $block, $column)
    {
        if ($position > 0 && isset($allColumns[$position-1])) {
            $thatColumn = $block->getColumn($column)->getData();
            $block->removeColumn($column);
            $columnBeforeThat = $allColumns[$position-1];
            $block->addColumnAfter($column, $thatColumn, $columnBeforeThat);
        }
    }

    protected function getUrl($url)
    {
        return Mage::getUrl($url);
    }

    /**
     * @return MageWorx_OrdersGrid_Helper_Data
     */
    protected function getMwHelper()
    {
        return Mage::helper('mageworx_ordersgrid');
    }
}