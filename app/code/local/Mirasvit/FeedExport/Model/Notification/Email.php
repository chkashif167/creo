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
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Model_Notification_Email extends Varien_Object
{
    public function onGenerationSuccess($observer)
    {
        $feed = $observer->getFeed();

        if (!$feed || !in_array('generation_success', $feed->getNotificationEvents())) {
            return $this;
        }

        $vars = array(
            'feed'             => $feed,
            'access_url'       => $feed->getUrl(),
            'generated_at'     => Mage::getSingleton('core/date')->date('m.d.Y H:i:s'),
            'generation_time'  => Mage::helper('feedexport')->timeSince($feed->getGeneratedTime()),
            'generation_count' => $feed->getGeneratedCnt(),

        );

        $template = Mage::getModel('core/email_template')->loadDefault('feedexport_generation_success');

        $template->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
        $template->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
        $template->send(explode(',', $feed->getNotificationEmails()),
            array(),
            $vars
        );

        return $this;
    }

    public function onDeliverySuccess($observer)
    {
        $feed = $observer->getFeed();

        if (!$feed || !in_array('delivery_success', $feed->getNotificationEvents())) {
            return $this;
        }

        $vars = array(
            'feed'         => $feed,
            'delivered_at' => Mage::getSingleton('core/date')->date('m.d.Y H:i:s'),

        );

        $template = Mage::getModel('core/email_template')->loadDefault('feedexport_delivery_success');

        $template->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
        $template->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
        $template->send(explode(',', $feed->getNotificationEmails()),
            array(),
            $vars
        );

        return $this;
    }

    public function onDeliveryFail($observer)
    {
        $feed  = $observer->getFeed();

        if (!$feed || !in_array('delivery_fail', $feed->getNotificationEvents())) {
            return $this;
        }

        $error = $observer->getError();
        $vars  = array(
            'feed'  => $feed,
            'error' => nl2br($error),
        );

        $template = Mage::getModel('core/email_template')->loadDefault('feedexport_delivery_fail');

        $template->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
        $template->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
        $template->send(explode(',', $feed->getNotificationEmails()),
            array(),
            $vars
        );

        return $this;
    }

    public function onGenerationFail($observer)
    {
        $feed  = $observer->getFeed();
        $error = $observer->getError();

        if (!$feed || !in_array('generation_fail', $feed->getNotificationEvents())) {
            return $this;
        }

        $vars  = array(
            'feed'  => $feed,
            'error' => nl2br($error),
        );

        $template = Mage::getModel('core/email_template')->loadDefault('feedexport_generation_fail');

        $template->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
        $template->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
        $template->send(explode(',', $feed->getNotificationEmails()),
            array(),
            $vars
        );

        return $this;
    }
}