<?php
/**
 * Yireo Common
 *
 * @author Yireo
 * @package Yireo_Common
 * @copyright Copyright 2015
 * @license Open Source License (OSL v3) (OSL)
 * @link http://www.yireo.com
 */
/**
 * Feed Model
 */

class Yireo_GoogleTranslate_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    /**
     * Return the feed URL
     */
    protected $customFeedUrl = 'https://www.yireo.com/extfeed?format=feed&platform=magento&extension=googletranslate';

    /**
     * Return the feed URL
     *
     * @return string
     */
    public function getFeedUrl()
    {
        return $this->customFeedUrl;
    }

    /**
     * Try to update feed
     *
     * @return mixed
     */
    public function updateIfAllowed()
    {
        // Is this the backend
        if (Mage::app()->getStore()->isAdmin() == false) {
            return false;
        }

        // Is the backend-user logged-in
        if (Mage::getSingleton('admin/session')->isLoggedIn() == false) {
            return false;
        }

        // Is the feed disabled?
        if ((bool)Mage::getStoreConfig('yireo/common/disabled')) {
            return false;
        }

        // Update the feed
        $this->checkUpdate();
        return true;
    }

    /**
     * Override the original method
     *
     * @return SimpleXMLElement
     */
    public function getFeedData()
    {
        // Get the original data
        $feedXml = parent::getFeedData();

        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            foreach ($feedXml->channel->item as $item) {

                // Add the severity to each item
                $feedXml->channel->item->severity = Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE;
            }
        }

        return $feedXml;
    }
}
