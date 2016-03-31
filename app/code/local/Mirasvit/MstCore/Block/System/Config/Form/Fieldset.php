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


class Mirasvit_MstCore_Block_System_Config_Form_Fieldset extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->frontend_class = 'mstcore';

        $this->setElement($element);
        $html = $this->_getHeaderHtml($element);

        foreach ($element->getSortedElements() as $field) {

            $helpText = $this->getHelpText($field);

            if ($helpText) {
                $field->setComment($helpText);
                $field->setHint('?');
            }

            $html.= $field->toHtml();
        }

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getFrontendClass($element)
    {
        return 'section-config mst-config';
    }

    public function getHelpText($field)
    {
        $arr = explode('_', $field->getId());
        $module = $arr[0];
        $code  = array();
        for ($i = 1; $i < count($arr); $i++) {
            $code[] = $arr[$i];
        }

        $code = implode('_', $code);

        $help = Mage::helper($module.'/help');

        $helpText = $help->getText('system', $code);

        return $helpText;
    }
}
