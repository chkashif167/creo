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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



/**
 * Блок вывода кнопки для управления демонов (стоп\старт...).
 *
 * @category Mirasvit
 */
class Mirasvit_SearchSphinx_Block_Adminhtml_System_BtnAction extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('searchsphinx/system/btn_action.phtml');
        }

        return $this;
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(array(
            'button_action' => $originalData['button_action'],
            'button_label' => $this->_getBtnLabel($originalData),
            'html_id' => $element->getHtmlId(),
            'ajax_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/searchsphinx_system_action/'.$originalData['button_action']),
        ));

        return $this->_toHtml();
    }

    protected function _getBtnLabel($originalData)
    {
        $label = $originalData['button_label'];

        switch ($originalData['button_action']) {
            case 'stopstart':
                $engine = Mage::getSingleton('searchsphinx/engine_sphinx_native');

                $isRunning = false;
                try {
                    $isRunning = $engine->isSearchdRunning();
                } catch (Exception $e) {
                }

                if ($isRunning) {
                    $label = 'Stop Sphinx daemon';
                } else {
                    $label = 'Start Sphinx daemon';
                }
                break;
        }

        return Mage::helper('searchsphinx')->__($label);
    }
}
