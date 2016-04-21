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
 * GoogleTranslate observer
 */
class Yireo_GoogleTranslate_Model_Observer_Abstract
{
    /**
     * Method to check whether a certain event is allowed
     *
     * @param $observer
     * @return bool
     */
    protected function allow($observer)
    {
        // If the configuration is told to disable this module, quit now
        if (Mage::helper('googletranslate')->enabled() == false) {
            return false;
        }

        // Get the parameters from the event
        $transport = $observer->getEvent()->getTransport();
        $block = $observer->getEvent()->getBlock();
        if (empty($block) || !is_object($block)) {
            return false;
        }

        // Check whether this block-object is of the right instance
        $allowedTypes = array(
            'adminhtml/catalog_form_renderer_fieldset_element',
            'adminhtml/widget_form_renderer_fieldset_element',
        );

        $allowedClasses = array(
            'Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element',
            'Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element',
        );

        $allowedElements = array(
            'Varien_Data_Form_Element_Text',
            'Varien_Data_Form_Element_Editor',
        );

        $isAllowedClass = false;
        foreach ($allowedClasses as $allowedClass) {
            if ($block instanceof $allowedClass) {
                $isAllowedClass = true;
            }
        }

        if ($isAllowedClass == false && in_array($block->getType(), $allowedTypes) == false) {
            return false;
        }

        $element = $block->getElement();

        $isAllowedElement = false;
        foreach ($allowedElements as $allowedElement) {
            if ($element instanceof $allowedElement) {
                $isAllowedElement = true;
            }
        }

        // Check if the form-element is text-input based
        if ($isAllowedElement == false && stristr(get_class($element), 'wysiwyg') == false) {
            return false;
        }

        return true;
    }

    /**
     * Method to return the data types for specific URLs
     *
     * @return string
     */
    protected function getDataType()
    {
        static $data_type = null;
        if (empty($data_type)) {
            $currentUrl = Mage::helper('core/url')->getCurrentUrl();

            if (stristr($currentUrl, 'cms_block/edit')) {
                $data_type = 'block';
            } elseif (stristr($currentUrl, 'cms_page/edit')) {
                $data_type = 'page';
            } elseif (stristr($currentUrl, 'catalog_category/edit')) {
                $data_type = 'category';
            } elseif (stristr($currentUrl, 'catalog_product/edit')) {
                $data_type = 'product';
            } else {
                $data_type = 'unknown';
            }
        }

        return $data_type;
    }
}
