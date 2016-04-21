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
class Yireo_GoogleTranslate_Helper_Observer extends Mage_Core_Helper_Abstract
{
    /**
     * Helper method to fetch the button-HTML
     *
     * @param int $id
     * @param string $label
     * @param bool $disabled
     * @param array $arguments
     * @return string
     */
    public function button($id, $label, $disabled = false, $arguments)
    {
        // Convert the button-arguments into a JavaScript-ready array
        $jsArgs = array();
        foreach ($arguments as $argument) {
            $jsArgs[] = '\'' . $argument . '\'';
        }

        // Construct the button HTML-code
        $html = Mage::getSingleton('core/layout')
            ->createBlock('adminhtml/widget_button', '', array(
                'label' => Mage::helper('googletranslate')->__($label),
                'type' => 'button',
                'disabled' => $disabled,
                'class' => ($disabled) ? 'googletranslate_button disabled' : 'googletranslate_button',
                'style' => 'margin-right:5px;margin-top:5px;',
                'id' => 'googletranslate_button_' . $id,
                'onclick' => 'YireoGoogleTranslate.translateAttribute(' . implode(',', $jsArgs) . ')'
            ))->toHtml();

        return $html;
    }

    /**
     * Helper method to fetch the button-HTML
     *
     * @param string $attribute_code
     * @param string $html_id
     * @return string
     */
    public function script($attribute_code, $html_id)
    {
        // Construct the button JavaScript-code
        $html = "<script type=\"text/javascript\">\n"
            . "Event.observe(window, 'load', function() {\n"
            . "    var button = $('googletranslate_button_" . $attribute_code . "');\n"
            . "    var field = $('" . $html_id . "');\n"
            . "    if(field && field.disabled) {\n"
            . "        button.disabled = true;\n"
            . "        button.className = 'disabled';\n"
            . "    }\n"
            . "});\n"
            . "</script>\n";

        return $html;
    }
}
