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



class Mirasvit_Email_IndexController extends Mage_Core_Controller_Front_Action
{
    public function unsubscribeAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            $queue = Mage::getModel('email/queue')->loadByUniqKeyMd5($code);

            if (!$queue) {
                Mage::getSingleton('core/session')->addError($this->__('Wrong unsubscription link'));
                $this->getResponse()->setRedirect($this->_getUrl('/', true));

                return;
            }

            Mage::getSingleton('email/unsubscription')->unsubscribe($queue->getRecipientEmail(), $queue->getTriggerId());

            Mage::getSingleton('core/session')->addSuccess($this->__('You have been successfully unsubscribed from receiving these emails.'));
        }

        $this->getResponse()->setRedirect($this->_getUrl('/', true));
    }

    public function unsubscribeAllAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            $queue = Mage::getModel('email/queue')->loadByUniqKeyMd5($code);

            if (!$queue) {
                Mage::getSingleton('core/session')->addError($this->__('Wrong unsubscription link'));
                $this->getResponse()->setRedirect($this->_getUrl('/', true));

                return;
            }

            Mage::getSingleton('email/unsubscription')->unsubscribe($queue->getRecipientEmail(), null);

            Mage::getSingleton('core/session')->addSuccess($this->__('You have been successfully unsubscribed from receiving these emails.'));
        }

        $this->getResponse()->setRedirect($this->_getUrl('/', true));
    }

    public function unsubscribeNewsletterAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            $queue = Mage::getModel('email/queue')->loadByUniqKeyMd5($code);

            if (!$queue) {
                Mage::getSingleton('core/session')->addError($this->__('Wrong unsubscription link'));
                $this->getResponse()->setRedirect($this->_getUrl('/', true));

                return;
            }

            Mage::getSingleton('email/unsubscription')->unsubscribe($queue->getRecipientEmail(), null);
            Mage::getSingleton('email/unsubscription')->unsubscribeNewsletter($queue->getRecipientEmail());

            Mage::getSingleton('core/session')->addSuccess($this->__('You have been successfully unsubscribed from receiving these emails.'));
        }

        $this->getResponse()->setRedirect($this->_getUrl('/', true));
    }

    public function restoreCartAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            Mage::helper('email/frontend')->loginCustomerByQueueCode($code);

            if (Mage::helper('email/frontend')->restoreCartByQueueCode($code)) {
                $this->getResponse()->setRedirect($this->_getUrl('checkout/cart', true));

                return;
            }
        }

        Mage::getSingleton('core/session')->addError($this->__('The cart for restore not found.'));
        $this->getResponse()->setRedirect($this->_getUrl('/', true));
    }

    public function resumeAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            Mage::helper('email/frontend')->loginCustomerByQueueCode($code);
        }

        if ($to = $this->getRequest()->getParam('to')) {
            if (base64_decode($to)) {
                $to = base64_decode($to);
            }

            $url = $this->_getUrl($to);
            // Place hash to the end of URL
            if (($hashPos = strpos($url, '#')) && strpos($url, '?') > $hashPos) {
                $fragment = substr($url, $hashPos, strpos($url, '?') - $hashPos);
                $url = str_replace($fragment, '', $url).$fragment;
            }

            $this->getResponse()->setRedirect($url);
        } else {
            $this->getResponse()->setRedirect($this->_getUrl('/', true));
        }
    }

    public function viewAction()
    {
        if (($queueId = $this->getRequest()->getParam('queue_id')) || ($code = $this->getRequest()->getParam('code'))) {
            if ($queueId) {
                $queue = Mage::getModel('email/queue')->load($queueId);
            } else {
                $queue = Mage::getModel('email/queue')->getCollection()
                    ->addFieldToFilter('uniq_key_md5', $code)
                    ->addFieldToFilter('status', Mirasvit_Email_Model_Queue::STATUS_DELIVERED)
                    ->getFirstItem();
            }

            if (!$queue) {
                Mage::getSingleton('core/session')->addError($this->__('The email not found.'));
                $this->getResponse()->setRedirect($this->_getUrl('/', true));

                return;
            }

            echo $queue->getContent();
        } else {
            Mage::getSingleton('core/session')->addError($this->__('The cart for restore not found.'));
            $this->getResponse()->setRedirect($this->_getUrl('/', true));
        }
    }

    public function captureAction()
    {
        $type = $this->getRequest()->getParam('type');
        $value = $this->getRequest()->getParam('value');

        $quote = Mage::getModel('checkout/cart')->getQuote();
        if ($quote->getBillingAddress() && $quote->getBillingAddress()->getId()) {
            $billing = $quote->getBillingAddress();
            $billing->setData($type, $value)
                ->save();
        }

        echo 'ok';
    }

    public function imageAction()
    {
        $path = $this->getRequest()->getParam('path');
        $size = $this->getRequest()->getParam('size');

        $url = Mage::helper('catalog/image')
            ->init(Mage::getModel('catalog/product'), 'image', $path);

        if (intval($size) > 0) {
            $url = $url->resize(intval($size));
        }

        $path = $url->__toString();

        $info = pathinfo($path);
        $ext = $info['extension'];

        header('Content-Type:'.'image/'.$ext);
        header('Content-Length: '.filesize($path));
        readfile($path);
    }

    protected function _getUrl($url, $full = false)
    {
        $params = array();
        foreach ($this->getRequest()->getParams() as $key => $value) {
            if (strpos($key, 'utm_') !== false) {
                $params[$key] = $value;
            }
        }

        if ($full) {
            $url = Mage::getUrl($url, array('_query' => $params));
        } else {
            $query = http_build_query($params);

            if ($query) {
                if (strpos($url, '?') !== false) {
                    $url .= '&'.$query;
                } else {
                    $url .= '?'.$query;
                }
            }
        }

        return $url;
    }
}
