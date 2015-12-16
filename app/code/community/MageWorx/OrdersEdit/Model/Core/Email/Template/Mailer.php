<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Core_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer
{

    /**
     * @param null $attachFilePath
     * @param null $attachFileName
     * @return $this
     */
    public function send($attachFilePath = null, $attachFileName = null)
    {
        $emailTemplate = Mage::getModel('core/email_template');
        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailInfo = array_pop($this->_emailInfos);
            // Handle "Bcc" recepients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());
            // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
            $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()));
               
                    
            if ($attachFilePath && $attachFileName) {
                $attachFileContents = file_get_contents($attachFilePath);                
                $attachment = $emailTemplate->getMail()->createAttachment($attachFileContents);            
                $attachment->filename = $attachFileName;
            }    
                
            $emailTemplate->sendTransactional(
                $this->getTemplateId(),
                $this->getSender(),
                $emailInfo->getToEmails(),
                $emailInfo->getToNames(),
                $this->getTemplateParams(),
                $this->getStoreId()
            );
        }     
        return $this;
    }
    
}
