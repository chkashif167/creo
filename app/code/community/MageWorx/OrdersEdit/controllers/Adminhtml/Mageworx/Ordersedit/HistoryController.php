<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

include_once('Mage/Adminhtml/controllers/Sales/OrderController.php');

class MageWorx_OrdersEdit_Adminhtml_Mageworx_Ordersedit_HistoryController extends Mage_Adminhtml_Sales_OrderController
{
    /**
     * Save new order comment
     */
    public function addCommentAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $order->addStatusHistoryComment($data['comment'], $data['status'])
                    ->setIsVisibleOnFront($visible)
                    ->setIsCustomerNotified($notify);

                $comment = trim(strip_tags($data['comment']));

                $order->save();

                // if send upload file
                if (isset($_FILES['send_file']['size']) && $_FILES['send_file']['size']>0) {

                    $histories = $order->getStatusHistoryCollection(true);
                    foreach ($histories as $h) {
                        $historyId =$h->getEntityId();
                        break;
                    }

                    $uploadFile = Mage::getModel('mageworx_ordersedit/upload_files')
                        ->setHistoryId($historyId)
                        ->setFileName($_FILES['send_file']['name'])
                        ->setFileSize($_FILES['send_file']['size'])
                        ->save();

                    $fileId = $uploadFile->getEntityId();
                    $filePath = $this->getMwHelper()->getUploadFilesPath($fileId, true);
                    copy($_FILES['send_file']['tmp_name'], $filePath);

                    $this->getMwHelper()->sendOrderUpdateEmail($order, $notify, $comment, $filePath, $uploadFile->getFileName());
                    return $this->_redirectReferer();
                }

                $order->sendOrderUpdateEmail($notify, $comment);

                $this->loadLayout('empty');
                $this->renderLayout();
            }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }

    /**
     * Delete order comment
     */
    public function deleteHistoryAction()
    {
        try {
            $response = false;
            $id = $this->getRequest()->getParam('id');

            Mage::getModel('mageworx_ordersedit/upload_files')->load($id, 'history_id')->removeFile();
            Mage::getModel('sales/order_status_history')->load($id)->delete();

            $this->_initOrder();
            $this->loadLayout('empty');
            $this->renderLayout();

        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->getMwHelper()->__('Failed to remove item.')
            );
        }

        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
            $this->getResponse()->setBody($response);
        }
    }

    /*
     * Save existing order comment (after edit)
     */
    public function saveEditCommentAction()
    {
        $commentId = $this->getRequest()->getPost('comment_id');
        $text = $this->getRequest()->getPost('comment');

        try {

            $this->_initOrder();

            $comment = Mage::getModel('sales/order_status_history')->load($commentId);
            $comment->setData('comment', $text);
            $comment->save();

            $this->loadLayout('empty');
            $this->renderLayout();

        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'ordersedit.log', true);
        }
    }

    /**
     * @return MageWorx_OrdersEdit_Helper_Data
     */
    protected function getMwHelper()
    {
        return Mage::helper('mageworx_ordersedit');
    }
}