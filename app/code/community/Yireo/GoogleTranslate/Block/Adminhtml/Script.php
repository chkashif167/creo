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
 * GoogleTranslate Script-block
 */
class Yireo_GoogleTranslate_Block_Adminhtml_Script extends Mage_Core_Block_Template
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setData('area', 'adminhtml');
    }

    /**
     * Return a specific URL
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = array())
    {
        return Mage::getModel('adminhtml/url')->getUrl($route, $params);
    }

    /**
     * Return the configured API key version 2
     *
     * @return string
     */
    public function getApiKey2()
    {
        return Mage::helper('googletranslate')->getApiKey2();
    }


    /**
     * Get the AJAX base URL for translating entities
     *
     * @return string
     */
    public function getAjaxEntityBaseUrl()
    {
        return $this->getUrl('adminhtml/googletranslate/' . $this->getPageType());
    }

    /**
     * Get the AJAX base URL for translating strings
     *
     * @return string
     */
    public function getAjaxTextBaseUrl()
    {
        return $this->getUrl('adminhtml/googletranslate/text');
    }
}
