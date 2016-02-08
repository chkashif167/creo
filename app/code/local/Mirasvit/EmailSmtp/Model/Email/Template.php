<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_EmailSmtp_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    public function send($email, $name = null, array $variables = array())
    {
        $emails = array_values((array) $email);
        $names = is_array($name) ? $name : (array) $name;
        $names = array_values($names);
        foreach ($emails as $key => $email) {
            if (!isset($names[$key])) {
                $names[$key] = substr($email, 0, strpos($email, '@'));
            }
        }

        $variables['email'] = reset($emails);
        $variables['name'] = reset($names);

        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        $mail = $this->getMail();

        $setReturnPath = Mage::getStoreConfig(self::XML_PATH_SENDING_SET_RETURN_PATH);

        switch ($setReturnPath) {
            case 1:
                $returnPathEmail = $this->getSenderEmail();
                break;
            case 2:
                $returnPathEmail = Mage::getStoreConfig(self::XML_PATH_SENDING_RETURN_PATH_EMAIL);
                break;
            default:
                $returnPathEmail = null;
                break;
        }

        if (Mage::getStoreConfig('emailsmtp/smtp/enabled')) {
            $config = array(
                'ssl' => Mage::getStoreConfig('emailsmtp/smtp/ssl'),
                'port' => Mage::getStoreConfig('emailsmtp/smtp/port'),
                'auth' => Mage::getStoreConfig('emailsmtp/smtp/auth'),
                'username' => Mage::getStoreConfig('emailsmtp/smtp/login'),
                'password' => Mage::getStoreConfig('emailsmtp/smtp/password'),
            );

            if ($config['ssl'] == 'none') {
                unset($config['ssl']);
            }

            $mailTransport = new Zend_Mail_Transport_Smtp(Mage::getStoreConfig('emailsmtp/smtp/host'), $config);
            Zend_Mail::setDefaultTransport($mailTransport);
        } elseif ($returnPathEmail !== null) {
            $mailTransport = new Zend_Mail_Transport_Sendmail('-f'.$returnPathEmail);
            Zend_Mail::setDefaultTransport($mailTransport);
        }

        foreach ($emails as $key => $email) {
            $mail->addTo($email, '=?utf-8?B?'.base64_encode($names[$key]).'?=');
        }

        $this->setUseAbsoluteLinks(true);
        $text = $this->getProcessedTemplate($variables, true);

        if ($this->isPlain()) {
            $mail->setBodyText($text);
        } else {
            $mail->setBodyHTML($text);
        }

        $subject = $this->getProcessedTemplateSubject($variables);
        $mail->setSubject('=?utf-8?B?'.base64_encode($subject).'?=');
        $mail->setFrom($this->getSenderEmail(), $this->getSenderName());

        if (Mage::getStoreConfig('emailsmtp/smtp/email_logging')) {
            $mailModel = Mage::getModel('emailsmtp/mail')
                ->setFromName($this->getSenderName())
                ->setFromEmail($this->getSenderEmail())
                ->setReplyTo($this->getReplyTo())
                ->setToName($variables['name'])
                ->setToEmail($variables['email'].' ['.implode(', ', array_diff($mail->getRecipients(), array($variables['email']))).']')
                ->setBody($text)
                ->setSubject($subject)
                ->save();
        }

        try {
            $mail->send();
            $this->_mail = null;
        } catch (Exception $e) {
            $this->_mail = null;
            Mage::logException($e);

            if (Mage::getStoreConfig('emailsmtp/smtp/email_logging')) {
                $mailModel->setMessage($e)->save();
            }

            return false;
        }

        return true;
    }
}
