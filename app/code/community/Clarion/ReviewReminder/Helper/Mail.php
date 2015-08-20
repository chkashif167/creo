<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    6th Jan, 2015
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    Review Reminder mail helper
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Clarion_ReviewReminder_Helper_Mail extends Mage_Core_Helper_Abstract
{
    const XML_PATH_EMAIL_TEMPLATE   = 'review_reminder/general_settings/email_template';
    const XML_PATH_EMAIL_SENDER     = 'review_reminder/general_settings/sender_email_identity';
    
    /**
     * Send reminder email
     *
     * @param Clarion_ReviewReminder_Model_Reviewreminder $reminder
     * @return boolean
     */
    public function sendReminderEmail($reminder)
    {
        //check is extension enabled
         if (!Mage::helper('clarion_reviewreminder')->isExtensionEnabled()) {
             return;
         }
         
        $customerId = $reminder->getCustomerId();
        if(!$customerId){
            return false;
        }
        
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $customerEmail = $customer->getEmail();
        $firstName = $customer->getFirstname();
        $productId = $reminder->getProductId();
        $product = Mage::getModel('catalog/product')->load($productId);
        $productName = $product->getName();
        $categoryId = $this->getProductCategoryId($product);
               
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
        try {
            $mailTemplate = Mage::getModel('core/email_template');
            /* @var $mailTemplate Mage_Core_Model_Email_Template */

            //get configured email template
            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, Mage::app()->getStore()->getId());

            $mailSender = Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, Mage::app()->getStore()->getId());
            
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>Mage::app()->getStore()->getId()))
                ->sendTransactional(
                $template,
                $mailSender,
                $customerEmail,
                $firstName,
                array(
                    'firstName' => $firstName,
                    'productId' => $productId,
                    'productName' => $productName,
                    'categoryId' => $categoryId
                )
            );
            
            if (!$mailTemplate->getSentSuccess()) {
                throw new Exception();
            }
                
            $translate->setTranslateInline(true);
            //Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('clarion_reviewreminder')->__('Your reminder sent successfully.'));
            //$this->_redirect('*/*/');
            return true;
        } catch (Exception $ex) {
            $translate->setTranslateInline(true);
            //Mage::getSingleton('adminhtml/session')->addError(Mage::helper('clarion_reviewreminder')->__('Unable to send reminder. Please, try again later'));
            // $this->_redirect('*/*/');
            return false;
        }
    }
    
    /**
     * Get product category id
     *
     * @param Mage_Catalog_Model_Product $product
     * @return boolean/int categoryId
     */
    public function getProductCategoryId($product) {
        /* @var $product Mage_Catalog_Model_Product */
        if ($product->getId()) {
            $categoryIds = $product->getCategoryIds();
            if (is_array($categoryIds) and count($categoryIds) > 0) {
                $categoryId = (isset($categoryIds[0]) ? $categoryIds[0] : null);
                return $categoryId;
            };
        }
        return false;
    }
}