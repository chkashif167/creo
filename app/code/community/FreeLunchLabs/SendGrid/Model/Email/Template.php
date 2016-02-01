<?php

class FreeLunchLabs_SendGrid_Model_Email_Template extends Mage_Core_Model_Email_Template {

    var $bcc = null;
    var $replyto = null;
    var $returnPath = null; //This is not used because SendGrid overides it for their internal purposes.

    public function send($email, $name = null, array $variables = array()) {
        if (Mage::getStoreConfig('sendgrid/general/active')) {
            if (!$this->isValidForSend()) {
                Mage::logException(new Exception('This letter cannot be sent.')); 
                return false;
            }

            //Rest Client
            $client = new Zend_Http_Client();
            $client->setUri('https://api.sendgrid.com/api/mail.send.json');
            $client->setMethod(Zend_Http_Client::POST);
            
            //Recipient(s)
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

            $is_first = true;
            foreach ($emails as $key => $email) {
                if ($is_first) {
                    $client->setParameterPost('to', $email);
                    $client->setParameterPost('toname', $names[$key]);
                } else {
                    $smtp_options['to'][] = $names[$key] . " <" . $email . ">";
                }

                $is_first = false;
            }
            
            //Subject
            $subject = $this->getProcessedTemplateSubject($variables);
            $client->setParameterPost('subject', $subject);
            
            //From Name
            $client->setParameterPost('from', $this->getSenderEmail());
            $client->setParameterPost('fromname', $this->getSenderName());

            //Bcc
            if (is_array($this->bcc)) {
                foreach ($this->bcc as $bcc_email) {
                    $client->setParameterPost('bcc[]', $bcc_email);
                }
            } elseif ($this->bcc) {
                $client->setParameterPost('bcc', $this->bcc);
            }

            //Reply To
            if (!is_null($this->replyto)) {
                $client->setParameterPost('replyto', $this->replyto);
            }

            //Message Body
            $this->setUseAbsoluteLinks(true);
            $processedTemplateBody = $this->getProcessedTemplate($variables, true);

            if ($this->isPlain()) {
                $client->setParameterPost('text', $processedTemplateBody);
            } else {
                $client->setParameterPost('html', $processedTemplateBody);
            }

            //Add Unique Args
            $smtp_options['unique_args'] = $this->buildUniqueArgs($variables);
            $smtp_options['unique_args']['email_subject'] = $subject;
            
            //Extra Header Options
            if(!is_null($smtp_options)) {
                $client->setParameterPost('x-smtpapi', json_encode($smtp_options));
            }
            
            //Set Post Params
            $client->setParameterPost('api_user', Mage::getStoreConfig('sendgrid/general/username'));
            $client->setParameterPost('api_key', Mage::getStoreConfig('sendgrid/general/password'));
            
            //Send it!
            try {
                $client->request();
                $this->_mail = null;
            } catch (Exception $e) {
                $this->_mail = null;
                Mage::logException($e);
                return false;
            }

            return true;
        } else {
            return parent::send($email, $name, $variables);
        }
    }

    public function addBcc($bcc) {
        if (Mage::getStoreConfig('sendgrid/general/active')) {
            $this->bcc = $bcc;
        } else {
            return parent::addBcc($bcc);
        }

        return $this;
    }

    public function setReturnPath($email) {
        if (Mage::getStoreConfig('sendgrid/general/active')) {
            $this->returnPath = $email;
        } else {
            return parent::setReturnPath($email);
        }

        return $this;
    }

    public function setReplyTo($email) {
        if (Mage::getStoreConfig('sendgrid/general/active')) {
            $this->replyto = $email;
        } else {
            return parent::setReplyTo($email);
        }

        return $this;
    }
    
    public function buildUniqueArgs($variables) {
        
        $unique_args = array();
        
        //Send email type
        $unique_args['email_type'] = $this->getTemplateId();
        
        //Send data in variable array
        foreach($variables as $key => $variable) {
            if(is_subclass_of($variable, 'Varien_Object')) {
                if(is_array($variable->getData())) {
                    foreach($variable->getData() as $dataKey => $dataItem) {
                        $unique_args[$key . " - " . $dataKey] = $dataItem;
                    }   
                }
            } else {
                $unique_args[$key] = $variable;
            }
        }
        
        return $unique_args;
    }

}
