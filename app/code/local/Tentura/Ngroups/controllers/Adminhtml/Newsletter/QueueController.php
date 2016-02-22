<?php

require_once 'Mage/Adminhtml/controllers/Newsletter/QueueController.php';

class Tentura_Ngroups_Adminhtml_Newsletter_QueueController extends Mage_Adminhtml_Newsletter_QueueController {

    public function saveAction() {
        
        try {

            $queue = Mage::getModel('newsletter/queue');
            // create new queue from template, if specified
            $templateId = $this->getRequest()->getParam('template_id');
            if ($templateId) {
                /* @var $template Mage_Newsletter_Model_Template */
                $template = Mage::getModel('newsletter/template')->load($templateId);

                if (!$template->getId() || $template->getIsSystem()) {
                    Mage::throwException($this->__('Wrong newsletter template.'));
                }

                $queue->setTemplateId($template->getId())
                        ->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_NEVER);
            } else {
                $queue->load($this->getRequest()->getParam('id'));
            }

            if (!in_array($queue->getQueueStatus(), array(Mage_Newsletter_Model_Queue::STATUS_NEVER,
                        Mage_Newsletter_Model_Queue::STATUS_PAUSE))
            ) {
                $this->_redirect('*/*');
                return;
            }

            if ($queue->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_NEVER) {
                $queue->setQueueStartAtByString($this->getRequest()->getParam('start_at'));
            }

            $queue->setStores($this->getRequest()->getParam('stores', array()))
                    ->setNewsletterSubject($this->getRequest()->getParam('subject'))
                    ->setNewsletterSenderName($this->getRequest()->getParam('sender_name'))
                    ->setNewsletterSenderEmail($this->getRequest()->getParam('sender_email'))
                    ->setUserGroup($this->getRequest()->getParam('user_group'))
                    ->setNewsletterText($this->getRequest()->getParam('text'))
                    ->setNewsletterStyles($this->getRequest()->getParam('styles'));

            if ($queue->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_PAUSE && $this->getRequest()->getParam('_resume', false)) {
                $queue->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_SENDING);
            }


            $queue->save();
            /*             exit; */
            Mage::getResourceModel('ngroups/queue')->addSubscribersToQueue($queue, array());
            $this->_redirect('*/*');
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $this->_redirect('*/*/edit', array('id' => $id));
            } else {
                $this->_redirectReferer();
            }
        }
    }

}
