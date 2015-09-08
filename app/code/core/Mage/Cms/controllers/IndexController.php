<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Cms index controller
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Renders CMS Home page
     *
     * @param string $coreRoute
     */
	 
	 const XML_PATH_EMAIL_RECIPIENT  = 'contacts/email/recipient_email';
     const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
     const XML_PATH_EMAIL_TEMPLATE   = 'contacts/email/email_template';
     const XML_PATH_ENABLED          = 'contacts/contacts/enabled';
	
    public function indexAction($coreRoute = null)
    {
	
        $pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE);
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('defaultIndex');
        }
    }
	
	public function postAction()
    {
		$post = $this->getRequest()->getPost();
		//echo "<pre>";print_r($post);
		//echo  $emailTemplate = Mage::getModel('core/email_template')->loadDefault('bulk_order');exit;
		if($post){
             
			$first_name = $post['first_name'];
			$last_name = $post['last_name'];
			$email = $post['email'];
			$phone = $post['phone'];
			$country = $post['country'];
			$city = $post['city'];
			$message = $post['message'];
			 
			$emailTemplate = Mage::getModel('core/email_template')->loadByCode('bulk_order');
			$emailTemplateVariables = array();
			$emailTemplateVariables['first_name'] = $first_name;
			$emailTemplateVariables['last_name'] = $last_name;
			$emailTemplateVariables['email'] = $email;
			$emailTemplateVariables['phone'] = $phone;
			$emailTemplateVariables['country'] = $country;
			$emailTemplateVariables['city'] = $city;
			$emailTemplateVariables['message'] = $message;
			//$emailTemplateVariables = array('first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'phone' => $phone, 'country' => $country, 'message' => $message);
			//echo "<pre>";print_r($emailTemplateVariables);echo "</pre>";
			$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			 //print_r($processedTemplate);exit;
			$toName = Mage::getStoreConfig('trans_email/ident_general/name');

			//Getting the Store General E-Mail.
			//$toEmail = Mage::getStoreConfig('trans_email/ident_general/email');
			$toEmail = 'talktous@creoroom.com';
			$toName = 'Creo support';
			$mail = Mage::getModel('core/email')
					 ->setToName($toName)
					 ->setToEmail($toEmail)
					 ->setBody($processedTemplate)
					 ->setSubject('Subject : Bulk Order')
					 ->setFromEmail($email)
					 ->setFromName($first_name)
					 ->setType('html');
			try{
			//Confimation E-Mail Send
			$mail->send();
			
			 Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your bulk order was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('bulk-orders');
				return;
			}
			 catch(Exception $error)
			 {
			 Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('bulk-orders'); 
                return;
			 }
           } 
    }



	public function customeremailAction()
    {
		
		$post = $this->getRequest()->getPost();
		//echo "<pre>";print_r($post);exit;
		//echo  $emailTemplate = Mage::getModel('core/email_template')->loadDefault('bulk_order');exit;
		if($post){
             
			$first_name = $post['first_name'];
			$last_name = $post['last_name'];
			$subject = $post['subject'];
			$email = $post['email'];
			$order_number = $post['order_number'];
			$message = $post['message'];
			 
			$emailTemplate = Mage::getModel('core/email_template')->loadByCode('customer_service_email');
			$emailTemplateVariables = array(); 
			$emailTemplateVariables['first_name'] = $first_name;
			$emailTemplateVariables['last_name'] = $last_name;
			$emailTemplateVariables['subject'] = $subject;
			$emailTemplateVariables['email'] = $email;
			$emailTemplateVariables['order_number'] = $order_number;
			$emailTemplateVariables['message'] = $message;
	
			$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			 //print_r($processedTemplate);exit;
			$toName = Mage::getStoreConfig('trans_email/ident_general/name');

			//Getting the Store General E-Mail.
			//$toEmail = Mage::getStoreConfig('trans_email/ident_general/email');
			$toEmail = 'talktous@creoroom.com';
			$toName = 'Creo support';
			$mail = Mage::getModel('core/email')
					 ->setToName($toName)
					 ->setToEmail($toEmail)
					 ->setBody($processedTemplate)
					 ->setSubject('Subject : Customer Service')
					 ->setFromEmail($email)
					 ->setFromName($first_name)
					 ->setType('html');
			try{ 
			//Confimation E-Mail Send
			$mail->send();
			
			 Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('customer-service');
				return;
			}
			 catch(Exception $error)
			 {
			 Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('customer-service');
                return;
			 }
           } 
    }
    /**
     * Default index action (with 404 Not Found headers)
     * Used if default page don't configure or available
     *
     */
    public function defaultIndexAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Render CMS 404 Not found page
     *
     * @param string $coreRoute
     */
    public function noRouteAction($coreRoute = null)
    {
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');

        $pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE);
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('defaultNoRoute');
        }
    }

    /**
     * Default no route page action
     * Used if no route page don't configure or available
     *
     */
    public function defaultNoRouteAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Render Disable cookies page
     *
     */
    public function noCookiesAction()
    {
        $pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE);
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('defaultNoCookies');;
        }
    }

    /**
     * Default no cookies page action
     * Used if no cookies page don't configure or available
     *
     */
    public function defaultNoCookiesAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
