<?php
/**
 * Yireo GoogleTranslate for Magento
 *
 * @package     Yireo_GoogleTranslate
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * GoogleTranslate helper
 */
class Yireo_GoogleTranslate_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Switch to determine whether the extension is enabled or not
     *
     * @return boolean
     */
    public function enabled()
    {
        if ((bool)Mage::getStoreConfig('advanced/modules_disable_output/Yireo_GoogleTranslate')) {
            return false;
        }

        if ($this->hasApiSettings() == false) {
            return false;
        }

        return true;
    }

    /**
     * Log a message
     *
     * @param type $message
     * @param type $variable
     *
     * @return type
     */
    public function log($message, $variable = null)
    {
        $logging = (bool)Mage::getStoreConfig('catalog/googletranslate/logging');
        if ($logging == false) {
            return false;
        }

        if (!empty($variable)) {
            $message .= ': ' . var_export($variable, true);
        }

        Mage::log($message, null, 'googletranslate.log');
    }

    /**
     * Check whether the API-details are configured
     *
     * @return string
     */
    public function hasApiSettings()
    {
        if(Mage::getStoreConfig('catalog/bingtranslate/bork')) {
            return true;
        }

        $apiKey = Mage::helper('googletranslate')->getApiKey2();

        if (empty($apiKey)) {
            return false;
        }

        return true;
    }

    /**
     * Return the API-key
     *
     * @return string
     */
    public function getApiKey2()
    {
        return Mage::getStoreConfig('catalog/googletranslate/apikey2');
    }

    /**
     * Return the customization ID
     *
     * @return string
     */
    public function getCustomizationId()
    {
        return Mage::getStoreConfig('catalog/googletranslate/customization_id');
    }

    /**
     * Return the text of the button label
     *
     * @return string
     */
    public function getButtonLabel()
    {
        $label = Mage::getStoreConfig('catalog/googletranslate/buttonlabel');
        $label = str_replace('$FROM', self::getFromTitle(), $label);
        $label = str_replace('$TO', self::getToTitle(), $label);

        return $label;
    }

    /**
     * Return the source language
     *
     * @return string
     */
    public function getFromLanguage()
    {
        $parent_locale = Mage::getStoreConfig('general/locale/code');
        $from_language = preg_replace('/_(.*)/', '', $parent_locale);
        return $from_language;
    }

    /**
     * Return the title of the source language
     *
     * @return string
     */
    public function getFromTitle()
    {
        $from_language = self::getFromLanguage();
        $from_title = Zend_Locale::getTranslation($from_language, 'language');
        return $from_title;
    }

    /**
     * Return the destination language
     *
     * @return string
     */
    public function getToLanguage($store = null)
    {
        if (empty($store)) {
            $store = Mage::app()->getRequest()->getUserParam('store');
        }

        $to_language = Mage::getStoreConfig('catalog/googletranslate/langcode', $store);
        if (empty($to_language)) {
            $to_language = $this->getLanguageFromStore($store);
        }

        $controllerName = Mage::app()->getRequest()->getControllerName();
        if ($controllerName == 'cms_block') {
            $blockId = Mage::app()->getRequest()->getParam('block_id');
            $storeId = $this->getStoreIdFromBlockId($blockId);
            $to_language = $this->getLanguageFromStore($storeId);

        } elseif ($controllerName == 'cms_page') {
            $pageId = Mage::app()->getRequest()->getParam('page_id');
            $storeId = $this->getStoreIdFromPageId($pageId);
            $to_language = $this->getLanguageFromStore($storeId);
        }

        return $to_language;
    }

    /**
     * Return the title of the destination language
     *
     * @return string
     */
    public function getToTitle()
    {
        $to_language = self::getToLanguage();
        $to_title = Zend_Locale::getTranslation($to_language, 'language');
        return $to_title;
    }

    /**
     * Get store from page
     *
     * @param mixed $pageId Page indicator
     *
     * @return string
     */
    public function getStoreIdFromPageId($pageId)
    {
        if ($pageId > 0) {
            $page = Mage::getModel('cms/page')->load($pageId);
            $storeIds = $page->getStoreId();
            if (is_array($storeIds) && count($storeIds) == 1) {
                $storeId = $storeIds[0];
                return $storeId;
            }
        }

        return false;
    }

    /**
     * Get store from block
     *
     * @param mixed $blockId Block indicator
     *
     * @return string
     */
    public function getStoreIdFromBlockId($blockId)
    {
        if ($blockId > 0) {
            $block = Mage::getModel('cms/block')->load($blockId);
            $storeIds = $block->getStoreId();
            if (is_array($storeIds) && count($storeIds) == 1) {
                $storeId = $storeIds[0];
                return $storeId;
            }
        }

        return false;
    }

    /**
     * Get language from a Store View
     *
     * @param mixed $store Store indicator (integer or Mage_Core_Model_Store)
     *
     * @return string
     */
    public function getLanguageFromStore($store)
    {
        $locale = Mage::getStoreConfig('general/locale/code', $store);
        $language = preg_replace('/_(.*)/', '', $locale);
        return $language;
    }

    /**
     * Return the title of the destination language
     *
     * @return string
     */
    public function getStoreByCode($code)
    {
        $stores = Mage::app()->getStores();
        foreach ($stores as $store) {
            if ($store->getCode() == $code) {
                return $store;
            }
        }
        return Mage::getModel('core/store');
    }
}
