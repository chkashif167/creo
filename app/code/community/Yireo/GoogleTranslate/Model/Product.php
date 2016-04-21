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
 * GoogleTranslate Product-extension
 */
class Yireo_GoogleTranslate_Model_Product extends Mage_Core_Model_Abstract
{
    /**
     * Allow translation
     *
     * @var boolean
     */
    protected $allowTranslation = true;

    /**
     * Counter of characters
     *
     * @var int
     */
    protected $charCount = 0;

    /**
     * Method to translate specific attributes of a specific product
     *
     * @param $product
     * @param $productAttributes
     * @param $stores
     * @param int $delay
     * @param bool $allowTranslation
     *
     */
    public function translate($product, $productAttributes, $stores, $delay = 0, $allowTranslation = null)
    {
        // Reset some values
        $this->charCount = 0;

        // Set the flag for translation
        if (is_bool($allowTranslation)) {
            $this->allowTranslation = $allowTranslation;
        }

        // Load the entire product
        $product = Mage::getModel('catalog/product')->load($product->getId());

        // Initialize the translator
        $translator = Mage::getSingleton('googletranslate/translator');

        // Get the parent-locale
        $parentLocale = Mage::getStoreConfig('general/locale/code');
        $parentLanguage = preg_replace('/_(.*)/', '', $parentLocale);

        // Loop through the stores
        foreach ($stores as $store) {

            if (!is_object($store)) {
                if (is_numeric($store)) {
                    $store = Mage::getModel('core/store')->load($store);
                } else {
                    $store = Mage::helper('googletranslate')->getStoreByCode($store);
                }
            }

            // Load the product into this store-scope
            $product->setStoreId($store->getId());

            $currentLanguage = Mage::helper('googletranslate')->getToLanguage($store);

            // Loop through the attributes
            foreach ($productAttributes as $productAttribute) {

                // Log
                $log = Mage::helper('googletranslate')->__('Translating attribute "%s" of "%s" for store "%s"', $productAttribute, $product->getSku(), $store->getName());
                Mage::helper('googletranslate')->log($log);

                // Reset some values
                $translatedValue = null;

                // Load both the global-value as the store-value
                $productValue = Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), $productAttribute, Mage_Core_Model_App::ADMIN_STORE_ID);
                $currentValue = Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), $productAttribute, $store);

                // Sanity checks
                $productValue = trim($productValue);
                $currentValue = trim($currentValue);

                if (empty($productValue)) {
                    Mage::helper('googletranslate')->log(Mage::helper('googletranslate')->__('Empty product value, so skipping'));
                    continue;
                }

                // Overwrite existing values
                if ($productValue != $currentValue) {
                    if ((bool)Mage::getStoreConfig('catalog/googletranslate/overwrite_existing') == false) {
                        Mage::helper('googletranslate')->log(Mage::helper('googletranslate')->__('Existing value, so skipping'));
                        continue;
                    }
                }

                // Translate the value
                if ($this->allowTranslation == true) {

                    $translatedValue = $translator->translate($productValue, $parentLanguage, $currentLanguage);
                    $apiError = $translator->getApiError();

                    if (!empty($apiError)) {
                        Mage::helper('googletranslate')->log(Mage::helper('googletranslate')->__('API-error for %s: %s', $product->getSku(), $apiError));
                    }

                    if (!empty($translatedValue)) {
                        $product->setData($productAttribute, $translatedValue);
                        $product->getResource()->saveAttribute($product, $productAttribute);
                    }
                }

                // Increment the total-chars
                $this->charCount = $this->charCount + strlen($productValue);
            }

            // Resave entire product
            if ($this->allowTranslation == true) {
                $product->save();
            }

            // Artificial sleep to give the API a rest
            if ($delay > 0) {
                sleep((int)$delay);
            }
        }
    }

    /**
     * Method to return the current character count
     *
     * @return int
     */
    public function getCharCount()
    {
        return $this->charCount;
    }

    /**
     * Method to toggle the flag which allows translation
     *
     * @param bool $allowTranslation
     * @return bool
     */
    public function allowTranslation($allowTranslation)
    {
        return $this->allowTranslation = (bool)$allowTranslation;
    }
}
