<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_ENABLED = 'mageworx_ordersmanagement/ordersgrid/enabled';

    const XML_ENABLE_INVOICE_ORDERS = 'mageworx_ordersmanagement/ordersgrid/enable_invoice_orders';
    const XML_SEND_INVOICE_EMAIL = 'mageworx_ordersmanagement/ordersgrid/send_invoice_email';
    const XML_ENABLE_SHIP_ORDERS = 'mageworx_ordersmanagement/ordersgrid/enable_ship_orders';
    const XML_SEND_SHIPMENT_EMAIL = 'mageworx_ordersmanagement/ordersgrid/send_shipment_email';

    const XML_ENABLE_ARCHIVE_ORDERS = 'mageworx_ordersmanagement/ordersgrid/enable_archive_orders';
    const XML_ARCHIVE_ORDERS_STATUS = 'mageworx_ordersmanagement/ordersgrid/archive_orders_status';
    const XML_DAYS = 'mageworx_ordersmanagement/ordersgrid/days_before_orders_get_archived';
    const XML_ENABLE_DELETE_ORDERS = 'mageworx_ordersmanagement/ordersgrid/enable_delete_orders';
    const XML_HIDE_DELETED_ORDERS_FOR_CUSTOMERS = 'mageworx_ordersmanagement/ordersgrid/hide_deleted_orders_for_customers';
    const XML_ENABLE_DELETE_ORDERS_COMPLETLY = 'mageworx_ordersmanagement/ordersgrid/enable_delete_orders_completely';

    const XML_GRID_COLUMNS = 'mageworx_ordersmanagement/ordersgrid/grid_columns';
    const XML_GRID_COLUMNS_SORT_ORDER = 'mageworx_ordersmanagement/ordersgrid/grid_columns_sort_order';
    const XML_CUSTOMER_GRID_COLUMNS = 'mageworx_ordersmanagement/ordersgrid/customer_grid_columns';
    const XML_CUSTOMER_GRID_COLUMNS_SORT_ORDER = 'mageworx_ordersmanagement/ordersgrid/customer_grid_columns_sort_order';


    protected $_contentType = 'application/octet-stream';
    protected $_resourceFile = null;
    protected $_handle = null;


    public function isEnabled()
    {
        return Mage::getStoreConfig(self::XML_ENABLED);
    }

    public function isEnableInvoiceOrders()
    {
        return Mage::getStoreConfig(self::XML_ENABLE_INVOICE_ORDERS);
    }

    public function isSendInvoiceEmail()
    {
        return Mage::getStoreConfig(self::XML_SEND_INVOICE_EMAIL);
    }

    public function isEnableShipOrders()
    {
        return Mage::getStoreConfig(self::XML_ENABLE_SHIP_ORDERS);
    }

    public function isSendShipmentEmail()
    {
        return Mage::getStoreConfig(self::XML_SEND_SHIPMENT_EMAIL);
    }

    public function isEnableArchiveOrders()
    {
        return Mage::getStoreConfig(self::XML_ENABLE_ARCHIVE_ORDERS);
    }

    public function isEnableDeleteOrders()
    {
        return Mage::getStoreConfig(self::XML_ENABLE_DELETE_ORDERS);
    }

    public function isHideDeletedOrdersForCustomers()
    {
        return Mage::getStoreConfig(self::XML_HIDE_DELETED_ORDERS_FOR_CUSTOMERS);
    }

    public function isEnableDeleteOrdersCompletely()
    {
        return Mage::getStoreConfig(self::XML_ENABLE_DELETE_ORDERS_COMPLETLY);
    }

    /**
     * @return int
     */
    public function getDaysBeforeOrderGetArchived()
    {
        return intval(Mage::getStoreConfig(self::XML_DAYS));
    }

    /**
     * @return array
     */
    public function getGridColumns()
    {
        $listColumns = Mage::getStoreConfig(self::XML_GRID_COLUMNS);
        $listColumns = explode(',', $listColumns);
        return $listColumns;
    }

    /**
     * @return array
     */
    public function getAllGridColumns()
    {
        $options = Mage::getModel('mageworx_ordersgrid/system_config_source_orders_grid')->toArray();
        return $options;
    }

    /**
     * @return array
     */
    public function getGridColumnsSortOrder()
    {
        $data = Mage::getStoreConfig(self::XML_GRID_COLUMNS_SORT_ORDER);
        $unsData = unserialize($data);
        return $unsData;
    }

    /**
     * @return array
     */
    public function getCustomerGridColumns()
    {
        $listColumns = Mage::getStoreConfig(self::XML_CUSTOMER_GRID_COLUMNS);
        $listColumns = explode(',', $listColumns);
        return $listColumns;
    }

    /**
     * @return array
     */
    public function getAllCustomerGridColumns()
    {
        $options = Mage::getModel('mageworx_ordersgrid/system_config_source_orders_customer_grid')->toArray();
        return $options;
    }

    /**
     * @return array
     */
    public function getCustomerGridColumnsSortOrder()
    {
        $data = Mage::getStoreConfig(self::XML_CUSTOMER_GRID_COLUMNS_SORT_ORDER);
        $unsData = unserialize($data);
        return $unsData;
    }

    public function getNumberComments()
    {
        return intval(Mage::getStoreConfig('mageworx_ordersmanagement/ordersgrid/number_comments'));
    }

    public function isShowThumbnails()
    {
        return Mage::getStoreConfig('mageworx_ordersmanagement/ordersgrid/show_thumbnails');
    }

    public function getThumbnailHeight()
    {
        return Mage::getStoreConfig('mageworx_ordersmanagement/ordersgrid/thumbnail_height');
    }

    /**
     * @return array
     */
    public function getArchiveOrderStatuses()
    {
        return explode(',', Mage::getStoreConfig(self::XML_ARCHIVE_ORDERS_STATUS));
    }

    /**
     * @param $orderIds
     * @param int $orderGroupId
     * @return int
     */
    public function addToOrderGroup($orderIds, $orderGroupId = 0)
    {
        /** @var Varien_Db_Adapter_Interface $connection */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();

        foreach ($orderIds as $orderId) {
            $connection->update($tablePrefix . 'sales_flat_order_grid', array('order_group_id' => intval($orderGroupId)), 'entity_id = ' . intval($orderId));
            $connection->update($tablePrefix . 'sales_flat_order', array('order_group_id' => intval($orderGroupId)), 'entity_id = ' . intval($orderId));
        }
        return count($orderIds);
    }

    /**
     * @param $orderIds
     * @return int
     */
    public function deleteOrderCompletely($orderIds)
    {
        foreach ($orderIds as $orderId) {
            $this->deleteOrderCompletelyById($orderId);
        }
        return count($orderIds);
    }

    /**
     * @param $order
     * @throws Exception
     */
    public function deleteOrderCompletelyById($order)
    {
        if (is_object($order)) {
            $orderId = $order->getId();
        } else {
            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load(intval($order), 'entity_id');
            $orderId = $order->getId();
        }

        if ($orderId) {
            // cancel
            try {
                $order->cancel()->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }

            // delete
            Mage::getResourceModel('mageworx_ordersgrid/order')->deleteOrderCompletely($order);
        }
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function invoiceOrder($order)
    {
        $savedQtys = array();
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getQtyToInvoice() > 0) {
                $savedQtys[$orderItem->getId()] = $orderItem->getQtyToInvoice();
            }
        }

        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
        if (!$invoice->getTotalQty()) {
            return false;
        };

        $invoice->setRequestedCaptureCase('online');
        $invoice->register();

        // if send email
        $sendEmailFlag = $this->isSendInvoiceEmail();
        if ($sendEmailFlag) {
            $invoice->setEmailSent(true);
        }

        $invoice->getOrder()->setCustomerNoteNotify($sendEmailFlag);
        $invoice->getOrder()->setIsInProcess(true);

        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());
        $transactionSave->save();

        // if send email
        $invoice->sendEmail($sendEmailFlag, '');

        return $invoice;
    }

    /**
     * @param $orderIds
     * @return int
     */
    public function invoiceOrderMass($orderIds)
    {
        $count = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if ($orderId > 0) {

                /** @var Mage_Sales_Model_Order $order */
                $order = Mage::getModel('sales/order')->load($orderId);
                if (!$order->getId()) {
                    continue;
                }
                if (!$order->canInvoice()) {
                    continue;
                }

                $invoice = $this->invoiceOrder($order);
                if ($invoice) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function shipOrder($orderIds) {
        $coreResource = Mage::getSingleton('core/resource');
        $write = $coreResource->getConnection('core_write');
        $count = 0;

        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if ($orderId>0) {
                try {
                    $order = Mage::getModel('sales/order')->load($orderId);
                    if (!$order->getId()) continue;
                    if ($order->getForcedDoShipmentWithInvoice()) continue;
                    if (!$order->canShip()) continue;

                    $savedQtys = array();
                    foreach ($order->getAllItems() as $orderItem) {
                        $savedQtys[$orderItem->getId()] = $orderItem->getQtyToShip();
                    }

                    $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);

                    // custom 2 line:
                    if (Mage::registry('current_shipment')) Mage::unregister('current_shipment');
                    Mage::register('current_shipment', $shipment);
                    Mage::app()->getRequest()->setPost('shipment', array('settings'=>array('profile'=>'standard', 'insurance'=>'0', 'personally'=>'0', 'bulkfreight'=>'0'), 'packages'=>array('package_0'=>array('weight'=>'0.5'))));


                    if (!$shipment) continue;
                    if (!$shipment->getTotalQty()) continue;


                    $shipment->register();

                    // if send email
                    $sendEmailFlag = $this->isSendShipmentEmail();
                    if ($sendEmailFlag) {
                        $shipment->setEmailSent(true);
                    }

                    $shipment->getOrder()->setCustomerNoteNotify($sendEmailFlag);
                    $shipment->getOrder()->setIsInProcess(true);





                    $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();

                    // custom line:
                    Mage::dispatchEvent('controller_action_postdispatch_adminhtml_sales_order_shipment_save', array('controller_action'=>$this));


                    // if send email
                    $shipment->sendEmail($sendEmailFlag, '');


                    // custom lines:
                    if (false && $shipment->getId()) {
                        // delete shipment comments
                        $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_shipment_comment')."` WHERE `parent_id`=".$shipment->getId());
                    }


                    $count++;
                } catch (Exception $e) {}
            }
        }
        return $count;
    }

    /**
     * translate and QuoteEscape
     *
     * @param $str
     * @return mixed
     */
    public function __js($str)
    {
        return $this->jsQuoteEscape(str_replace("\'", "'", $this->__($str)));
    }

    /**
     * @return array|mixed
     */
    public function getAllPaymentMethods()
    {
        if (Mage::registry('payment_methods')) {
            return Mage::registry('payment_methods');
        }
        $payments = Mage::getSingleton('payment/config')->getAllMethods();
        $methods = array();
        foreach ($payments as $paymentCode => $paymentModel) {
            $methods[$paymentCode] = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
        }
        Mage::register('payment_methods', $methods);
        return $methods;
    }

    /**
     * @return array|mixed
     */
    public function getAllShippingMethods()
    {
        if (Mage::registry('shipping_methods')) {
            return Mage::registry('shipping_methods');
        }
        $carriers = Mage::getSingleton('shipping/config')->getAllCarriers();
        $methods = array();
        foreach ($carriers as $code => $carriersModel) {
            $title = Mage::getStoreConfig('carriers/' . $code . '/title');
            if ($title) {
                $methods[$code . '_' . $code] = $title;
            }
        }
        Mage::register('shipping_methods', $methods);
        return $methods;
    }

    /**
     * @return array|mixed
     */
    public function getCustomerGroups()
    {
        if (Mage::registry('customer_groups')) {
            return Mage::registry('customer_groups');
        }
        $customerGroups = Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
        $groups = array();
        foreach ($customerGroups as $data) {
            $groups[$data['value']] = $data['label'];
        }
        Mage::register('customer_groups', $groups);
        return $groups;
    }

    /**
     * @return mixed
     */
    public function getOrderGroups()
    {
        if (Mage::registry('order_groups')) {
            return Mage::registry('order_groups');
        }
        $orderGroups = Mage::getResourceModel('mageworx_ordersgrid/order_group_collection')->load()->toOptionArray();
        Mage::register('order_groups', $orderGroups);
        return $orderGroups;
    }

    /**
     * @return array|mixed
     */
    public function getShippedStatuses()
    {
        if (Mage::registry('shipped_statuses')) {
            return Mage::registry('shipped_statuses');
        }
        $statuses = Mage::getModel('adminhtml/system_config_source_yesno')->toArray();
        Mage::register('shipped_statuses', $statuses);
        return $statuses;
    }

    /**
     * @return array|mixed
     */
    public function getEditedStatuses()
    {
        if (Mage::registry('edited_statuses')) {
            return Mage::registry('edited_statuses');
        }
        $statuses = array('1' => $this->__('Yes'), '0' => $this->__('No'));
        Mage::register('edited_statuses', $statuses);
        return $statuses;
    }

    /**
     * @param $item
     * @return bool
     */
    public function getImgByItem($item)
    {
        $productId = $item->getProductId();
        //getRealProductType
        //getParentItem
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->setStoreId($item->getStoreId())->load($productId);
        if ($product->getThumbnail() && $product->getThumbnail() != 'no_selection') {
            try {
                return Mage::helper('catalog/image')->init($product, 'thumbnail');
            } catch (Exception $e) {
                return false;
            }
        }
        if ($product->getTypeId() == 'configurable') {
            $childrens = $item->getChildrenItems();
            if (count($childrens) > 0) {
                $productId = $childrens[0]->getProductId();
                if ($productId) {
                    $product = Mage::getModel('catalog/product')->setStoreId($item->getStoreId())->load($productId);
                    if ($product->getThumbnail() && $product->getThumbnail() != 'no_selection') {
                        try {
                            return Mage::helper('catalog/image')->init($product, 'thumbnail');
                        } catch (Exception $e) {
                            return false;
                        }
                    }
                }
            }
        } else {
            if ($product->getThumbnail() && $product->getThumbnail() != 'no_selection') {
                try {
                    return Mage::helper('catalog/image')->init($product, 'thumbnail');
                } catch (Exception $e) {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isMagentoEnterprise()
    {
        $isEnterprise = false;
        $i = Mage::getVersionInfo();
        if ($i['major'] == 1) {
            if (method_exists('Mage', 'getEdition')) {
                if (Mage::getEdition() == Mage::EDITION_ENTERPRISE) {
                    $isEnterprise = true;
                }
            } elseif ($i['minor'] > 7) {
                $isEnterprise = true;
            }
        }
        return $isEnterprise;
    }

    /**
     * @return string
     */
    public function getMagentoVersion()
    {
        $i = Mage::getVersionInfo();
        if ($i['major'] == 1 && $this->isMagentoEnterprise()) {
            $i['minor'] -= 5;
        }
        return trim("{$i['major']}.{$i['minor']}.{$i['revision']}" . ($i['patch'] != '' ? ".{$i['patch']}" : "") . "-{$i['stability']}{$i['number']}", '.-');
    }

    /**
     * Check module and class (optional)
     *
     * @param  string       $module
     * @param  null|string  $class
     * @return bool
     */
    public static function foeModuleCheck($module, $class = null, $rewriteClass = null)
    {
        $module = (string)$module;
        if ($module && (string)Mage::getConfig()->getModuleConfig($module)->active == 'true') {
            if ($class && $rewriteClass) {
                return is_subclass_of($class, $rewriteClass);
            } elseif ($class && !$rewriteClass) {
                return class_exists($class);
            }
            return true;
        }
        return false;
    }

    /**
     * Save serialized sort order data is system config
     *
     * @param string $for - customer or grid
     * @param array $data
     * @return bool
     */
    public function saveSortOrderConfig($for, $data)
    {
        switch ($for) {
            case 'customer' :
                $path = self::XML_CUSTOMER_GRID_COLUMNS_SORT_ORDER;
                break;
            case 'grid' :
                $path = self::XML_GRID_COLUMNS_SORT_ORDER;
                break;
        }

        if (!isset($path)) {
            return false;
        }

        $value = serialize($data);

        /** @var Mage_Core_Model_Config $config */
        $config = Mage::getSingleton('core/config');
        $config->saveConfig($path, $value);

        return true;
    }

    /**
     * @return array|mixed
     */
    public function getCountryNames()
    {
        if (Mage::registry('country_names')) {
            return Mage::registry('country_names');
        }
        $countryNames = array();
        $collection = Mage::getResourceModel('directory/country_collection')->load();
        foreach ($collection as $item) {
            if ($item->getCountryId()) {
                $countryNames[$item->getCountryId()] = $item->getName();
            }
        }
        asort($countryNames);
        Mage::register('country_names', $countryNames);
        return $countryNames;
    }
}