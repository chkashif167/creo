<?php

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class EaDesign_PdfGenerator_Model_Core_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer
{

    public function send()
    {

        $emailTemplate = Mage::getModel('core/email_template');
        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailInfo = array_pop($this->_emailInfos);
            $this->dispatchEadesignPdfEvent($emailTemplate);
            // Handle "Bcc" recepients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());
            // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
            $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
                ->sendTransactional(
                    $this->getTemplateId(), $this->getSender(), $emailInfo->getToEmails(), $emailInfo->getToNames(), $this->getTemplateParams(), $this->getStoreId()
                );
        }
        return $this;
    }

    public function dispatchEadesignPdfEvent($emailTemplate)
    {

        $storeId = $this->getStoreId();
        $templateParams = $this->getTemplateParams();

        if ($templateParams['invoice']) {
            Mage::dispatchEvent(
                'eadesign_pdfgenerator_before_send_invoice', array(
                    'template' => $emailTemplate,
                    'object' => $templateParams['invoice']
                )
            );
        }
    }

}