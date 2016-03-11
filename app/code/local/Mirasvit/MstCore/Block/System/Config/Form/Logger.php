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



class Mirasvit_MstCore_Block_System_Config_Form_Logger extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        foreach ($element->getElements() as $field) {
            $html .= $field->toHtml();
        }

        $url = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/mstcore_logger/index');
        $html .= '
            <tr>
                <td class="label"></td>
                <td class="value">
                <button onclick="window.location=\''.$url.'\'" type="button">
                    <span>Display log</span>
                </button
                </td>
            </tr>
            ';
        $html .= $this->_getFooterHtml($element);

        return $html;
    }
}
