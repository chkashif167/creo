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



class Mirasvit_Email_Model_Queue extends Mage_Core_Model_Abstract
{
    const STATUS_PENDING       = 'pending';
    const STATUS_DELIVERED     = 'delivered';
    const STATUS_CANCELED      = 'canceled';
    const STATUS_UNSUBSCRIBED  = 'unsubscribed';
    const STATUS_ERROR         = 'error';
    const STATUS_MISSED        = 'missed';

    const GENERATED_MODE_CRON = 'cron';
    const GENERATED_MODE_MANUAL = 'manual';

    protected $_args = null;
    protected $_trigger = null;
    protected $_chain   = null;

    protected function _construct()
    {
        $this->_init('email/queue');
    }

    public function loadByUniqKeyMd5($code)
    {
        $queue = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('uniq_key_md5', $code)
            ->getFirstItem();

        if ($queue->getId()) {
            return Mage::getModel('email/queue')->load($queue->getId());
        }

        return false;
    }

    public function getTrigger()
    {
        if ($this->_trigger == null) {
            $this->_trigger = Mage::getModel('email/trigger')->load($this->getTriggerId());
        }

        return $this->_trigger;
    }

    public function getChain()
    {
        if ($this->_chain == null) {
            $this->_chain = Mage::getModel('email/trigger_chain')->load($this->getChainId());
        }

        return $this->_chain;
    }

    public function getTemplate()
    {
        return $this->getChain()->getTemplate();
    }

    public function getArgs()
    {
        if ($this->_args == null) {
            $this->_args = unserialize($this->getData('args_serialized'));
            $this->_args['trigger'] = $this->getTrigger();
            $this->_args['chain'] = $this->getChain();
            $this->_args['queue'] = $this;
        }

        return $this->_args;
    }

    public function getEmailSubject()
    {
        if ($this->getData('subject') == '') {
            $subject = $this->getTemplate()->getProcessedTemplateSubject($this->getArgs());
            $this->setData('subject', $subject);
        }

        return $this->getData('subject');
    }

    public function getEmailContent()
    {
        if ($this->getData('content') == '') {
            $content = $this->getTemplate()->getProcessedTemplate($this->getArgs());

            $this->setData('content', $content);
            
            Mage::dispatchEvent('email_queue_get_content_after', array('queue' => $this));
        }

        return $this->getData('content');
    }

    public function getRecipientEmail()
    {
        $recipient = $this->getData('recipient_email');

        if (Mage::getSingleton('email/config')->isSandbox()) {
            $recipient = Mage::helper('email')->determineEmails(Mage::getSingleton('email/config')->getSandboxEmail());
        }

        if ($this->getTrigger()->getIsTriggerSandboxActive()) {
            $recipient = Mage::helper('email')->determineEmails($this->getTrigger()->getTriggerSandboxEmail());
        }

        if (is_array($recipient)) {
            $recipient = implode (',', $recipient);
        }

        return $recipient;
    }


    public function send()
    {
        $args = $this->getArgs();

        if (!isset($args['is_test'])) {
            // change status to missed after 2 days
            if ($this->isMissed()) {
                $this->miss("Scheduled At $this->getScheduledAt(), attempt to send after 2 days");

                return $this;
            }

            // check unsubscription
            if (Mage::getSingleton('email/unsubscription')->isUnsubscribed($this->getRecipientEmail(), $this->getTriggerId())) {
                $this->unsubscribe("Customer $this->getRecipientEmail() is unsubscribed");

                return $this;
            }

            // check rules
            if (!$this->getTrigger()->validateRules($args)) {
                $this->cancel('Canceled by trigger rules');

                return $this;
            }

            // check limitation
            if (!$this->isValidByLimit($args)) {
                $this->cancel('Canceled by global limitation settings');

                return $this;
            }
        }


        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($args['store_id']);

        $email = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);


        if (!$this->getTemplate()) {
            $this->cancel('Missed Template');

            return $this;
        }

        if ($this->getTemplate()->getDesign()
            && $this->getTemplate()->getDesign()->getTemplateType() == Mirasvit_EmailDesign_Model_Design::TEMPLATE_TYPE_TEXT) {
            $email->setTemplateType(Mage_Core_Model_Template::TYPE_TEXT);
        }

        $email->setReplyTo($this->getSenderEmail());
        $email->setSenderName($this->getSenderName());
        $email->setSenderEmail($this->getSenderEmail());

        $email->setTemplateSubject($this->getEmailSubject());
        if ($this->getTest()) {
            $email->setTemplateSubject($this->getEmailSubject().' ['.'Test Store #'.$this->_args['store_id'].' '.microtime(true).']');
        }
        $email->setTemplateText($this->getEmailContent());

        $recipient = $this->getRecipientEmail();
        $recipient = explode(',', $recipient);

        $copyTo = Mage::helper('email')->determineEmails($this->getTrigger()->getCopyEmail());
        foreach ($copyTo as $bcc) {
            $email->addBcc($bcc);
        }

        $result = $email->send(
            $recipient,
            $this->getRecipientName(),
            array(
                'name'    => $this->getRecipientName(),
                'email'   => $recipient,
                'subject' => $this->getEmailSubject(),
                'message' => $this->getEmailContent()
            )
        );

        $this->delivery();

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $result;
    }

    /**
     * If a queue was generated by cron and was not sent within 2 days it is missed.
     *
     * @return bool
     */
    public function isMissed()
    {
        $args = $this->getArgs();
        $isMissed = false;

        if (time() - strtotime($this->getScheduledAt()) > 60 * 60 * 24 * 2 &&
            (!isset($args['generated_mode']) || $args['generated_mode'] === self::GENERATED_MODE_CRON)
        ) {
            $isMissed = true;
        }

        return $isMissed;
    }

    public function pending($message = '')
    {
        $this->setStatus(self::STATUS_PENDING)
            ->setStatusMessage($message)
            ->save();
    }

    public function delivery($message = '')
    {
        $this->setSentAt(Mage::getSingleton('core/date')->gmtDate())
            ->setStatus(self::STATUS_DELIVERED)
            ->setStatusMessage($message)
            ->save();
    }

    public function miss($message = '')
    {
        $this->setStatus(self::STATUS_MISSED)
            ->setStatusMessage($message)
            ->save();
    }

    public function cancel($message = '')
    {
        $this->setStatus(self::STATUS_CANCELED)
            ->setStatusMessage($message)
            ->save();

        return $this;
    }

    public function error($message = '')
    {
        $this->setStatus(self::STATUS_ERROR)
            ->setStatusMessage($message)
            ->save();

        return $this;
    }

    public function unsubscribe($message = '')
    {
        $this->setStatus(self::STATUS_UNSUBSCRIBED)
            ->setStatusMessage($message)
            ->save();

        return $this;
    }

    public function reset($message = '')
    {
        $this->setStatus(self::STATUS_PENDING)
            ->setStatusMessage($message)
            ->setSentAt(null)
            ->setContent(null)
            ->save();

        return $this;
    }

    public function isValidByLimit($args)
    {
        $result = true;
        $emailLimit = Mage::getModel('email/config')->getEmailLimit();
        $hourLimit = Mage::getModel('email/config')->getEmailLimitPeriod() * 60 * 60;
        if (in_array(0, array($emailLimit, $hourLimit))) {
            return $result;
        }

        $gmtTimestampMinusLimit = Mage::getSingleton('core/date')->timestamp() - $hourLimit;
        $filterDateFrom = Mage::getSingleton('core/date')->gmtDate(null, $gmtTimestampMinusLimit);

        $queues = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('recipient_email', $args['customer_email'])
            ->addFieldToFilter('status', self::STATUS_DELIVERED)
            ->addFieldToFilter('updated_at', array('gt' => $filterDateFrom));

        if ($queues->count() >= $emailLimit) {
            $result = false;
        }

        return $result;
    }
}
