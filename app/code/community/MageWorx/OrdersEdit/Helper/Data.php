<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_ENABLED = 'mageworx_ordersmanagement/ordersedit/enabled';

    const XML_HIDE_EDIT_BUTTON = 'mageworx_ordersmanagement/ordersedit/hide_edit_button';
    const XML_ENABLE_INVOICE_ORDERS = 'mageworx_ordersmanagement/ordersedit/enable_invoice_orders';
    const XML_SEND_INVOICE_EMAIL = 'mageworx_ordersmanagement/ordersedit/send_invoice_email';
    const XML_ENABLE_SHIP_ORDERS = 'mageworx_ordersmanagement/ordersedit/enable_ship_orders';
    const XML_SEND_SHIPMENT_EMAIL = 'mageworx_ordersmanagement/ordersedit/send_shipment_email';

    const XML_ENABLE_ARCHIVE_ORDERS = 'mageworx_ordersmanagement/ordersedit/enable_archive_orders';
    const XML_ENABLE_DELETE_ORDERS = 'mageworx_ordersmanagement/ordersedit/enable_delete_orders';
    const XML_HIDE_DELETED_ORDERS_FOR_CUSTOMERS = 'mageworx_ordersmanagement/ordersedit/hide_deleted_orders_for_customers';
    const XML_ENABLE_DELETE_ORDERS_COMPLETLY = 'mageworx_ordersmanagement/ordersedit/enable_delete_orders_completely';

    const XML_GRID_COLUMNS = 'mageworx_ordersmanagement/ordersedit/grid_columns';
    const XML_CUSTOMER_GRID_COLUMNS = 'mageworx_ordersmanagement/ordersedit/customer_grid_columns';

    const XML_SEND_UPDATE_EMAIL = 'mageworx_ordersmanagement/ordersedit/send_update_email';
    const XML_ENABLE_SHIPPING_PRICE_EDITION = 'mageworx_ordersmanagement/ordersedit/enable_shipping_price_edition';
    const XML_SHOW_ALL_STATES_IN_HISTORY = 'mageworx_ordersmanagement/ordersedit/show_all_states_in_history';

    protected $_contentType = 'application/octet-stream';
    protected $_resourceFile = null;
    protected $_handle = null;


    public function isEnabled()
    {
        return Mage::getStoreConfig(self::XML_ENABLED);
    }

    public function isHideEditButton()
    {
        return Mage::getStoreConfig(self::XML_HIDE_EDIT_BUTTON);
    }

    public function isShippingPriceEditEnabled()
    {
        return Mage::getStoreConfig(self::XML_ENABLE_SHIPPING_PRICE_EDITION);
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

    public function isSendUpdateEmail()
    {
        return Mage::getStoreConfig(self::XML_SEND_UPDATE_EMAIL);
    }

    public function isNeedToShowAllStates()
    {
        return Mage::getStoreConfig(self::XML_SHOW_ALL_STATES_IN_HISTORY);
    }

    /**
     * @return array|mixed
     */
    public function getGridColumns()
    {
        $listColumns = Mage::getStoreConfig(self::XML_GRID_COLUMNS);
        $listColumns = explode(',', $listColumns);
        return $listColumns;
    }

    /**
     * @return array|mixed
     */
    public function getCustomerGridColumns()
    {
        $listColumns = Mage::getStoreConfig(self::XML_CUSTOMER_GRID_COLUMNS);
        $listColumns = explode(',', $listColumns);
        return $listColumns;
    }

    /**
     * @return int
     */
    public function getNumberComments()
    {
        return intval(Mage::getStoreConfig('mageworx_ordersmanagement/ordersedit/number_comments'));
    }

    public function isShowThumbnails()
    {
        return Mage::getStoreConfig('mageworx_ordersmanagement/ordersedit/show_thumbnails');
    }

    public function getThumbnailHeight()
    {
        return Mage::getStoreConfig('mageworx_ordersmanagement/ordersedit/thumbnail_height');
    }

    /** Return count of deleted orders
     *
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
     * @param Mage_Sales_Model_Order|int $order
     * @throws Exception
     */
    public function deleteOrderCompletelyById($order)
    {
        /** @var Mage_Core_Model_Resource $coreResource */
        $coreResource = Mage::getSingleton('core/resource');
        $write = $coreResource->getConnection('core_write');
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
            }
            // delete            
            if ($order->getQuoteId()) {
                $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_quote') . "` WHERE `entity_id`=" . $order->getQuoteId());
                $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_quote_address') . "` WHERE `quote_id`=" . $order->getQuoteId());
                $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_quote_item') . "` WHERE `quote_id`=" . $order->getQuoteId());
                $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_quote_payment') . "` WHERE `quote_id`=" . $order->getQuoteId());
            }
            $order->delete();
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_grid') . "` WHERE `entity_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_address') . "` WHERE `parent_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_item') . "` WHERE `order_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_payment') . "` WHERE `parent_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_payment_transaction') . "` WHERE `order_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_status_history') . "` WHERE `parent_id`=" . $orderId);

            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_invoice') . "` WHERE `order_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_creditmemo') . "` WHERE `order_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_shipment') . "` WHERE `order_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_order_tax') . "` WHERE `order_id`=" . $orderId);


            if (Mage::getConfig()->getModuleConfig('AW_Booking')->is('active', true)) {
                $write->query("DELETE FROM `" . $coreResource->getTableName('aw_booking_orders') . "` WHERE `order_id`=" . $orderId);
            }

        }
    }

    /**
     * @param $fileId
     * @param bool|false $createFolder
     * @return string
     */
    public function getUploadFilesPath($fileId, $createFolder = false)
    {
        // 3 byte -> 8 chars
        $fileId = '00000000' . $fileId;
        $fileId = substr($fileId, strlen($fileId) - 8, 8);
        $dir = substr($fileId, 0, 5);
        $file = substr($fileId, 5);

        $catalog = Mage::getBaseDir('media') . DS . 'ordersedit' . DS;

        if ($createFolder && !file_exists($catalog)) {
            mkdir($catalog);
        }

        if ($createFolder && !file_exists($catalog . $dir . DS)) {
            mkdir($catalog . $dir . DS);
        }

        return $catalog . $dir . DS . $file;
    }

    /**
     * @param $fileId
     * @return null|string
     */
    public function isUploadFile($fileId)
    {
        $file = $this->getUploadFilesPath($fileId, false);
        if (file_exists($file)) {
            return $file;
        } else {
            return null;
        }
    }

    /**
     * @param $fileId
     * @param $fileName
     * @return string
     */
    public function getUploadFilesUrl($fileId, $fileName)
    {
        // ordersedit/dl/file/id/1/file.png
        return $this->_getUrl('mageworx_ordersedit/dl/') . 'file/id/' . $fileId . '/' . $fileName;
    }

    /**
     * @param $size
     * @return string
     */
    public function prepareFileSize($size)
    {

        if ($size >= 1048576) {
            return round($size / 1048576, 2) . ' ' . $this->__('MB');
        } elseif ($size >= 1024) {
            return round($size / 1024, 2) . ' ' . $this->__('KB');
        } else {
            return $size . ' ' . $this->__('B');
        }
    }

    /**
     * @param $resource
     * @param $fileName
     * @throws Zend_Controller_Response_Exception
     */
    public function processDownload($resource, $fileName)
    {
        $this->_resourceFile = $resource;

        $response = Mage::app()->getResponse();
        $response->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $this->getContentType($fileName), true);

        if ($fileSize = $this->_getHandle()->streamStat('size')) {
            $response->setHeader('Content-Length', $fileSize);
        }
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->clearBody();
        $response->sendHeaders();

        $this->output();
    }

    /**
     * @return null|Varien_Io_File
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function _getHandle()
    {
        if (!$this->_resourceFile) {
            Mage::throwException($this->__('Please set resource file and link type'));
        }
        if (is_null($this->_handle)) {
            $this->_handle = new Varien_Io_File();
            $this->_handle->open(array('path' => Mage::getBaseDir('var')));
            if (!$this->_handle->fileExists($this->_resourceFile, true)) {
                Mage::throwException($this->__('File does not exist'));
            }
            $this->_handle->streamOpen($this->_resourceFile, 'r');
        }
        return $this->_handle;
    }

    /**
     * @return Mage_Core_Model_Config_Element|string
     */
    public function getContentType()
    {
        $this->_getHandle();
        if (function_exists('mime_content_type')) {
            return mime_content_type($this->_resourceFile);
        } else {
            return $this->getFileType($this->_resourceFile);
        }
    }

    /**
     * @param $fileName
     * @return Mage_Core_Model_Config_Element|string
     */
    public function getFileType($fileName)
    {
        $ext = substr($fileName, strrpos($fileName, '.') + 1);
        $type = Mage::getConfig()->getNode('global/mime/types/x' . $ext);
        if ($type) {
            return $type;
        }
        return $this->_contentType;
    }

    /**
     * Print
     */
    public function output()
    {
        $handle = $this->_getHandle();
        while ($buffer = $handle->streamRead()) {
            print $buffer;
        }
    }

    /**
     * @param Mage_Sales_Model_Order $orders
     * @param bool|true $notifyCustomer
     * @param string $comment
     * @param null $filePath
     * @param null $fileName
     * @return $this
     */
    public function sendOrderUpdateEmail($orders, $notifyCustomer = true, $comment = '', $filePath = null, $fileName = null)
    {
        $storeId = $orders->getStore()->getId();

        if (!Mage::helper('sales')->canSendOrderCommentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails('sales_email/order_comment/copy_to', $storeId);
        $copyMethod = Mage::getStoreConfig('sales_email/order_comment/copy_method', $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
        if ($orders->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig('sales_email/order_comment/guest_template', $storeId);
            $customerName = $orders->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig('sales_email/order_comment/template', $storeId);
            $customerName = $orders->getCustomerName();
        }

        /** @var MageWorx_OrdersEdit_Model_Core_Email_Template_Mailer $mailer */
        $mailer = Mage::getModel('mageworx_ordersedit/core_email_template_mailer');

        if ($notifyCustomer) {
            /** @var Mage_Core_Model_Email_Info $emailInfo */
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($orders->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig('sales_email/order_comment/identity', $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order' => $orders,
                'comment' => $comment,
                'billing' => $orders->getBillingAddress()
            )
        );
        $mailer->send($filePath, $fileName);

        return $this;
    }

    /**
     * @param $configPath
     * @param $storeId
     * @return array|bool
     */
    protected function _getEmails($configPath, $storeId)
    {
        $data = Mage::getStoreConfig($configPath, $storeId);
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
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
     * @param $orderIds
     * @param $status
     * @param string $comment
     * @param int $isVisibleOnFront
     * @param bool|false $isCustomerNotified
     * @return int
     */
    public function changeStatusOrder($orderIds, $status, $comment = '', $isVisibleOnFront = 1, $isCustomerNotified = false)
    {
        $count = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if ($orderId > 0) {
                try {
                    /** @var Mage_Sales_Model_Order $order */
                    $order = Mage::getModel('sales/order')->load($orderId);
                    if (!$order->getId()) {
                        continue;
                    }

                    $order->addStatusHistoryComment($comment, $status)
                        ->setIsVisibleOnFront($isVisibleOnFront)
                        ->setIsCustomerNotified($isCustomerNotified);

                    if ($isCustomerNotified) {
                        $comment = trim(strip_tags($comment));
                        $order->sendOrderUpdateEmail($isCustomerNotified, $comment);
                    }


                    $order->save();
                    $count++;
                } catch (Exception $e) {
                }
            }
        }
        return $count;
    }

    /** translate and QuoteEscape
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
        $orderGroups = Mage::getResourceModel('mageworx_ordersedit/order_group_collection')->load()->toOptionArray();
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
        $statuses = array('1' => $this->__('Yes'), '0' => $this->__('No'));
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
        $product = Mage::getModel('catalog/product')->setStoreId($item->getStoreId())->load($productId);
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
}