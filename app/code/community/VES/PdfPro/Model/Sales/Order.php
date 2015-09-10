<?php
/**
 * VES_PdfPro_Model_Sales_Order
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Model_Sales_Order extends Mage_Sales_Model_Order
{
	/**
     * Send email with order data
     *
     * @return Mage_Sales_Model_Order
     */
    public function sendNewOrderEmail()
    {
    	if(!Mage::getStoreConfig('pdfpro/config/enabled')) return parent::sendNewOrderEmail();
    	switch(Mage::getStoreConfig('pdfpro/config/order_email_attach')){
    		case VES_PdfPro_Model_Source_Attach::ATTACH_TYPE_NO:
    			return parent::sendNewOrderEmail();
    		case VES_PdfPro_Model_Source_Attach::ATTACH_TYPE_ADMIN:
    			$this->sendNewOrderEmailForAdmin(true);
    			$this->sendNewOrderEmailForCustomer(false);
    			return $this;
    		case VES_PdfPro_Model_Source_Attach::ATTACH_TYPE_CUSTOMER:
    			$this->sendNewOrderEmailForAdmin(false);
    			$this->sendNewOrderEmailForCustomer(true);
    			return $this;
    	}
        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        // Start store emulation process
        $appEmulation = Mage::getSingleton('pdfpro/app_emulation');
        if($appEmulation) $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
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
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        $mailer = Mage::getModel('pdfpro/email_template_mailer');
        $emailInfo = Mage::getModel('pdfpro/email_info');
        $emailInfo->addTo($this->getCustomerEmail(), $customerName);
        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $emailInfo->addBcc($email);
            }
        }
        $mailer->addEmailInfo($emailInfo);

        // Email copies are sent as separated emails if their copy method is 'copy'
        if ($copyTo && $copyMethod == 'copy') {
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
                'order'        => $this,
                'billing'      => $this->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            )
        );
        /* Attach order PDF in email */
       	$orderData= Mage::getModel('pdfpro/order')->initOrderData($this);
	   	try{
			$result = Mage::helper('pdfpro')->initPdf(array($orderData),'order');
			if($result['success']){
				$mailer->setPdf(array('filename'=>Mage::helper('pdfpro')->getFileName('order',$this).'.pdf', 'content'=>$result['content']));
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
    
    public function sendNewOrderEmailForCustomer($attachPdfFile = true){
    	$storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
            return $this;
        }

        // Start store emulation process
        $appEmulation = Mage::getSingleton('pdfpro/app_emulation');
        if($appEmulation) $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
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
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        $mailer = Mage::getModel('pdfpro/email_template_mailer');
        $emailInfo = Mage::getModel('pdfpro/email_info');
        $emailInfo->addTo($this->getCustomerEmail(), $customerName);

        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $this,
                'billing'      => $this->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            )
        );
        if($attachPdfFile){
	        /* Attach order PDF in email */
	       	$orderData= Mage::getModel('pdfpro/order')->initOrderData($this);
		   	try{
				$result = Mage::helper('pdfpro')->initPdf(array($orderData),'order');
				if($result['success']){
					$mailer->setPdf(array('filename'=>Mage::helper('pdfpro')->getFileName('order',$this).'.pdf', 'content'=>$result['content']));
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
    
    
    
    public function sendNewOrderEmailForAdmin($attachPdfFile = true){
    	$storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        // Start store emulation process
        $appEmulation = Mage::getSingleton('pdfpro/app_emulation');
        if($appEmulation) $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
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
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        $mailer = Mage::getModel('pdfpro/email_template_mailer');

        // Email copies are sent as separated emails if their copy method is 'copy'
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
                'order'        => $this,
                'billing'      => $this->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            )
        );
        
        if($attachPdfFile){
	        /* Attach order PDF in email */
	       	$orderData= Mage::getModel('pdfpro/order')->initOrderData($this);
		   	try{
				$result = Mage::helper('pdfpro')->initPdf(array($orderData),'order');
				if($result['success']){
					$mailer->setPdf(array('filename'=>Mage::helper('pdfpro')->getFileName('order',$this).'.pdf', 'content'=>$result['content']));
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
	/**
     * Retrieve text formated price value includeing order rate
     *
     * @param   float $price
     * @return  string
     */
    public function formatPriceTxt($price)
    {
        $orderCurrencyCode = $this->getOrderCurrencyCode();
    	return Mage::helper('pdfpro')->currency($price,$orderCurrencyCode);
    }
}