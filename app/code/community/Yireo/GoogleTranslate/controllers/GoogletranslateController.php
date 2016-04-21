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
 * GoogleTranslate admin controller
 *
 * @category   GoogleTranslate
 * @package     Yireo_GoogleTranslate
 */
class Yireo_GoogleTranslate_GoogletranslateController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Common method
     *
     * @return Yireo_GoogleTranslate_IndexController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/tools/googletranslate')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tools'), Mage::helper('adminhtml')->__('Tools'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Google Translate'), Mage::helper('adminhtml')->__('Google Translate'));
        return $this;
    }

    /**
     * Batch page
     */
    public function batchAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('googletranslate/adminhtml_batch'))
            ->renderLayout();
    }

    /**
     * Translate a specific product
     */
    public function translateProductAction()
    {
        $data = explode('|', $this->getRequest()->getParam('data'));
        $productId = $data[0];
        $storeId = $data[1];
        $attributeCode = $data[2];

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() > 0) {
            return $this->sendError($this->__('No product loaded for ID ' . $productId));
        }

        $store = Mage::getModel('core/store')->load($storeId);
        if (!$store->getId() > 0) {
            return $this->sendError($this->__('No store loaded for ID ' . $storeId));
        }

        $translator = Mage::getModel('googletranslate/product');
        $translator->translate($product, array($attributeCode), array($store));
        $charCount = $translator->getCharCount();

        return $this->sendMessage($this->__('Translated attribute "%s" for SKU "%s" in Store View "%s" (%s characters)', $attributeCode, $product->getSku(), $store->getCode(), $charCount));
    }

    /**
     * AJAX callback for regular text
     *
     * @return mixed
     */
    public function textAction()
    {
        $string = $this->getRequest()->getParam('string');
        $fromLang = $this->getRequest()->getParam('from');
        $toLang = $this->getRequest()->getParam('to');

        // Sanity checks
        if (empty($string)) {
            return $this->sendError($this->__('No text value given'));
        }

        if (empty($fromLang)) {
            return $this->sendError($this->__('No value for parameter from'));
        }

        if (empty($toLang)) {
            return $this->sendError($this->__('No value for parameter to'));
        }

        // Set the source language to empty, if it is the same as the destination language
        if ($fromLang == $toLang) {
            $fromLang = null;
        }

        // Check for enabled 
        $enabled = Mage::helper('googletranslate')->enabled();
        if ($enabled == false) {
            return $this->sendError($this->__('Not enabled'));
        }

        // Set these variables for use with the translator
        $translator = $this->getTranslator();
        $translator->setData('text', $string);
        $translator->setData('fromLang', $fromLang);
        $translator->setData('toLang', $toLang);
        $translator->setData('apiKey', $apiKey);

        // Load the correct data-model
        $translator = $this->getTranslator();
        $translator->setData('text', $string);

        // Make the request to the API
        $this->translate();
        return null;
    }

    /**
     * AJAX callback for products
     *
     * @return mixed
     */
    public function productAction()
    {
        // Load the initial data, and don't continue if this fails
        if ($this->preload() == false) {
            return null;
        }

        // Load the correct data-model
        $translator = $this->getTranslator();
        $id = $translator->getData('id');
        $store = $translator->getData('store');

        $product = Mage::getModel('catalog/product')->setStoreId($store)->load($id);
        if (!$product->getId() > 0) {
            return $this->sendError($this->__('No product ID given'));
        }

        // Load the attribute-value
        $attribute = $translator->getData('attribute');
        $text = $product->getData($attribute);

        if (empty($text)) {
            return $this->sendError($this->__('No product-data found for attribute %s', $attribute));
        }

        $translator->setData('text', $text);

        // Make the request to the API
        $this->translate();
        return null;
    }

    /**
     * AJAX callback for categories
     *
     * @return mixed
     */
    public function categoryAction()
    {
        // Load the initial data, and don't continue if this fails
        if ($this->preload() == false) {
            return null;
        }

        // Load the correct data-model
        $translator = $this->getTranslator();
        $id = $translator->getData('id');
        $store = $translator->getData('store');

        $category = Mage::getModel('catalog/category')->setStoreId($store)->load($id);
        if (!$category->getId() > 0) {
            return $this->sendError($this->__('No category ID given'));
        }

        // Load the attribute-value
        $attribute = $translator->getData('attribute');
        $text = $category->getData($attribute);
        if (empty($text)) {
            return $this->sendError($this->__('No category-data found for attribute %s', $attribute));
        }

        $translator->setData('text', $text);

        // Make the request to the API
        $this->translate();
        return null;
    }

    /**
     * AJAX callback for CMS-pages
     *
     * @return mixed
     */
    public function pageAction()
    {
        // Load the initial data, and don't continue if this fails
        if ($this->preload() == false) {
            return null;
        }

        // Load the correct data-model
        $translator = $this->getTranslator();
        $id = $translator->getData('id');
        $store = $translator->getData('store');

        $page = Mage::getModel('cms/page')->setStoreId($store)->load($id);
        if (!$page->getId() > 0) {
            return $this->sendError($this->__('No CMS-page ID given'));
        }

        // Load the attribute-value
        $attribute = $translator->getData('attribute');
        $text = $page->getData($attribute);
        if (empty($text)) {
            return $this->sendError($this->__('No page-data found for attribute %s', $attribute));
        }
        $translator->setData('text', $text);

        // Make the request to the API
        $this->translate();
        return null;
    }

    /**
     * AJAX callback for CMS-blocks
     *
     * @return mixed
     */
    public function blockAction()
    {
        // Load the initial data, and don't continue if this fails
        if ($this->preload() == false) {
            return null;
        }

        // Load the correct data-model
        $translator = $this->getTranslator();
        $id = $translator->getData('id');
        $store = $translator->getData('store');
        $block = Mage::getModel('cms/block')->setStoreId($store)->load($id);
        if (!$block->getId() > 0) {
            return $this->sendError($this->__('No CMS-block ID given'));
        }

        // Load the attribute-value
        $attribute = $translator->getData('attribute');
        $text = $block->getData($attribute);
        if (empty($text)) {
            return $this->sendError($this->__('No block-data found for attribute %s', $attribute));
        }
        $translator->setData('text', $text);

        // Make the request to the API
        $this->translate();
        return null;
    }

    /**
     * Perform some sanity checks
     *
     * @return mixed
     */
    protected function preload()
    {
        $id = $this->getRequest()->getParam('id');
        $attribute = $this->getRequest()->getParam('attribute');
        $fromLang = $this->getRequest()->getParam('from');
        $toLang = $this->getRequest()->getParam('to');
        $store = $this->getRequest()->getParam('store');

        // Sanity checks
        if (!$id > 0 || empty($attribute) || empty($fromLang) || empty($toLang)) {
            return $this->sendError($this->__('Wrong parameters'));
        }

        // Set the language to empty, when 
        if ($fromLang == $toLang) {
            $fromLang = null;
        }

        // Check for enabled
        $enabled = Mage::helper('googletranslate')->enabled();
        if (empty($enabled)) {
            return $this->sendError($this->__('Not enabled'));
        }

        // Set these variables for use with the translator
        $translator = $this->getTranslator();
        $translator->setData('id', $id);
        $translator->setData('attribute', $attribute);
        $translator->setData('fromLang', $fromLang);
        $translator->setData('toLang', $toLang);
        $translator->setData('store', $store);
        $translator->setData('apiKey', Mage::helper('googletranslate')->getApiKey2());

        return true;
    }

    /**
     * Method to return the translator object
     *
     * @return Yireo_GoogleTranslate_Model_Translator
     */
    public function getTranslator()
    {
        return Mage::getSingleton('googletranslate/translator');
    }

    /**
     * Method to call upon the API
     *
     * @return string
     */
    protected function translate()
    {
        $translator = $this->getTranslator();
        $text = $translator->translate();

        if ($translator->hasApiError()) {
            return $this->sendError($translator->getApiError());
        }

        return $this->sendTranslation($text);
    }

    /**
     * Helper method to send a success
     *
     * @param string $message
     * @return boolean
     */
    protected function sendMessage($message = null)
    {
        $result = array('message' => $message);
        $this->sendJsonResponse($result);
        return true;
    }

    /**
     * Helper method to send a specific error
     *
     * @param string $message
     * @return boolean
     */
    protected function sendError($message = null)
    {
        $result = array('error' => $message);
        $this->sendJsonResponse($result);
        return false;
    }

    /**
     * Helper method to send the translation
     *
     * @param string $translation
     * @return boolean
     */
    protected function sendTranslation($translation = null)
    {
        $result = array('translation' => $translation);
        $this->sendJsonResponse($result);
        return true;
    }


    /**
     * Helper method to send the JSON headers
     *
     * @params array $data
     */
    protected function sendJsonResponse($data)
    {
        $jsonData = Mage::helper('core')->jsonEncode($data);

        $this->getResponse()->setHeader('Content-type', 'application/json; charset=utf-8');
        $this->getResponse()->setBody($jsonData);
    }

    /**
     * Allow ACL access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/googletranslate');
    }
}

