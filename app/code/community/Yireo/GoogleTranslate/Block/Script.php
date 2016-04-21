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
class Yireo_GoogleTranslate_Block_Script extends Mage_Core_Block_Template
{
    /**
     * Return the customization ID
     *
     * @return string
     */
    public function getCustomizationId()
    {
        return Mage::helper('googletranslate')->getCustomizationId();
    }

    /**
     * Allow translation
     *
     * @return bool
     */
    public function allowTranslation()
    {
        return true; // @todo: Disable on specific pages?
    }
}
