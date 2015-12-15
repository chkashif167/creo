<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Order extends Mage_Sales_Model_Order
{

    /**
     * @param bool|true $notifyCustomer
     * @param string $comment
     * @return $this|MageWorx_OrdersEdit_Model_Order
     */
    public function sendOrderEditEmail($notifyCustomer = true, $comment = '')
    {
        if (!version_compare(Mage::getVersion(), '1.5.0.0', '>=')) {
            return $this->sendOrderEditEmailOld($notifyCustomer, $comment);
        }

        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendOrderCommentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = 'ordersedit_email_order_comment_guest_template';
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = 'ordersedit_email_order_comment_template';
            $customerName = $this->getCustomerName();
        }

        /** @var Mage_Core_Model_Email_Template_Mailer $mailer */
        $mailer = Mage::getModel('core/email_template_mailer');

        if ($notifyCustomer) {
            /** @var Mage_Core_Model_Email_Info $emailInfo */
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($this->getCustomerEmail(), $customerName);
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
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order' => $this,
                'comment' => $comment,
                'billing' => $this->getBillingAddress()
            )
        );
        $mailer->send();

        return $this;
    }

    /**
     * @param bool|true $notifyCustomer
     * @param string $comment
     * @return $this
     */
    public function sendOrderEditEmailOld($notifyCustomer = true, $comment = '')
    {
        if (!Mage::helper('sales')->canSendOrderCommentEmail($this->getStore()->getId())) {
            return $this;
        }

        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $this->getStoreId());
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // set design parameters, required for email (remember current)
        $currentDesign = Mage::getDesign()->setAllGetOld(array(
            'store' => $this->getStoreId(),
            'area' => 'frontend',
            'package' => Mage::getStoreConfig('design/package/name', $this->getStoreId()),
        ));

        /** @var Mage_Core_Model_Translate $translate */
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $sendTo = array();

        /** @var Mage_Core_Model_Email_Template $mailTemplate */
        $mailTemplate = Mage::getModel('core/email_template');

        if ($this->getCustomerIsGuest()) {
            $template = 'ordersedit_email_order_comment_guest_template';
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $template = 'ordersedit_email_order_comment_template';
            $customerName = $this->getCustomerName();
        }

        if ($notifyCustomer) {
            $sendTo[] = array(
                'name' => $customerName,
                'email' => $this->getCustomerEmail()
            );
            if ($copyTo && $copyMethod == 'bcc') {
                foreach ($copyTo as $email) {
                    $mailTemplate->addBcc($email);
                }
            }

        }

        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'name' => null,
                    'email' => $email
                );
            }
        }

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $this->getStoreId()),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'order' => $this,
                        'billing' => $this->getBillingAddress(),
                        'comment' => $comment
                    )
                );
        }

        $translate->setTranslateInline(true);

        // revert current design
        Mage::getDesign()->setAllGetOld($currentDesign);

        return $this;
    }

}
