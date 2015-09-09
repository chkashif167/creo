<?php
/**
 * VES_PdfPro_Model_Sales_Order_Invoice
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Model_Sales_Order_Invoice extends Mage_Sales_Model_Order_Invoice
{
    /**
     * Send email with invoice data
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        if(!Mage::getStoreConfig('pdfpro/config/enabled')) return parent::sendEmail($notifyCustomer, $comment);
    	switch(Mage::getStoreConfig('pdfpro/config/invoice_email_attach')){
    		case VES_PdfPro_Model_Source_Attach::ATTACH_TYPE_NO:
    			return parent::sendEmail();
    		case VES_PdfPro_Model_Source_Attach::ATTACH_TYPE_ADMIN:
    			$this->sendEmailForAdmin($notifyCustomer,$comment, true);
    			$this->sendEmailForCustomer($notifyCustomer,$comment,false);
    			return $this;
    		case VES_PdfPro_Model_Source_Attach::ATTACH_TYPE_CUSTOMER:
    			$this->sendEmailForAdmin($notifyCustomer,$comment,false);
    			$this->sendEmailForCustomer($notifyCustomer,$comment,true);
    			return $this;
    	}
    	$order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewInvoiceEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Start store emulation process
        $appEmulation = Mage::getSingleton('pdfpro/app_emulation');
        if($appEmulation) $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            if($appEmulation) $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        if($appEmulation) $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('pdfpro/email_template_mailer');
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('pdfpro/email_info');
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
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
                $emailInfo = Mage::getModel('pdfpro/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'invoice'      => $this,
                'comment'      => $comment,
                'billing'      => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            )
        );
        /* Attach Invoice PDF in email */
    	$invoiceData = Mage::getModel('pdfpro/order_invoice')->initInvoiceData($this);
	   	try{
			$result = Mage::helper('pdfpro')->initPdf(array($invoiceData));
			if($result['success']){
				$mailer->setPdf(array('filename'=>Mage::helper('pdfpro')->getFileName('invoice',$this).'.pdf', 'content'=>$result['content']));
			}else{
				Mage::log($result['msg']);
			}
		}catch(Exception $e){
			Mage::log($e->getMessage());
		}
        
        $mailer->send();
        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }
    
    public function sendEmailForAdmin($notifyCustomer=true,$comment='', $attachPdf = true){
    	$order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewInvoiceEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Start store emulation process
        $appEmulation = Mage::getSingleton('pdfpro/app_emulation');
        if($appEmulation) $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            if($appEmulation) $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        if($appEmulation) $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('pdfpro/email_template_mailer');

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        foreach ($copyTo as $email) {
        	$emailInfo = Mage::getModel('pdfpro/email_info');
        	$emailInfo->addTo($email);
        	$mailer->addEmailInfo($emailInfo);
        }
        
        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'invoice'      => $this,
                'comment'      => $comment,
                'billing'      => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            )
        );
        
        if($attachPdf){
	        /* Attach Invoice PDF in email */
	    	$invoiceData = Mage::getModel('pdfpro/order_invoice')->initInvoiceData($this);
		   	try{
				$result = Mage::helper('pdfpro')->initPdf(array($invoiceData));
				if($result['success']){
					$mailer->setPdf(array('filename'=>Mage::helper('pdfpro')->getFileName('invoice',$this).'.pdf', 'content'=>$result['content']));
				}else{
					Mage::log($result['msg']);
				}
			}catch(Exception $e){
				Mage::log($e->getMessage());
			}
        }
        $mailer->send();
        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }
    
	public function sendEmailForCustomer($notifyCustomer=true,$comment='', $attachPdf = true){
    	$order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewInvoiceEmail($storeId)) {
            return $this;
        }

        // Check if at least one recepient is found
        if (!$notifyCustomer ) {
            return $this;
        }

        // Start store emulation process
        $appEmulation = Mage::getSingleton('pdfpro/app_emulation');
        if($appEmulation) $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            if($appEmulation) $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        if($appEmulation) $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('pdfpro/email_template_mailer');
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('pdfpro/email_info');
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            $mailer->addEmailInfo($emailInfo);
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'invoice'      => $this,
                'comment'      => $comment,
                'billing'      => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            )
        );
        if($attachPdf){
	        /* Attach Invoice PDF in email */
	    	$invoiceData = Mage::getModel('pdfpro/order_invoice')->initInvoiceData($this);
		   	try{
				$result = Mage::helper('pdfpro')->initPdf(array($invoiceData));
				if($result['success']){
					$mailer->setPdf(array('filename'=>Mage::helper('pdfpro')->getFileName('invoice',$this).'.pdf', 'content'=>$result['content']));
				}else{
					Mage::log($result['msg']);
				}
			}catch(Exception $e){
				Mage::log($e->getMessage());
			}
        }
        $mailer->send();
        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }
}
