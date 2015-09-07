<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Config
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Wysiwyg_Config extends Mage_Cms_Model_Wysiwyg_Config
{

    /**
     *
     * @param Varien_Object
     * @return Varien_Object
     */
    public function getConfig($data = array())
    {
        $config = parent::getConfig($data);

        $newOptiones = Mage::getSingleton('eadesign/variables_optiones')->getWysiwygPluginSettings($config);

        if (isset($newOptiones['plugins'][1]) && is_array($newOptiones['plugins'][1])) {
            $config->setData('plugins', array($newOptiones['plugins'][1]));
        }

        $config->setData('files_browser_window_url', Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index/'));
        $config->setData('directives_url', Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'));
        $config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));
        $config->setData('widget_window_url', Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index'));
        $config->setData('add_variables', true);

        return $config;
    }

}
