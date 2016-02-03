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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailDesign_Block_Template extends Mage_Core_Block_Abstract
{
    public function __call($method, $args)
    {
        $helpers = Mage::getSingleton('emaildesign/config')->getVariablesHelpers();
        foreach ($helpers as $helperCode) {
            $helper = Mage::helper($helperCode);
            if (method_exists($helper, $method)) {
                return call_user_func(array($helper, $method), $this, $args);
            }
        }

        return parent::__call($method, $args);
    }


    public function area($area, $default = false)
    {
        if ($this->hasData('area_'.$area)) {
            $tplContent = $this->getData('area_'.$area);
            $block = Mage::app()->getLayout()->createBlock('emaildesign/template');

            return $block->render($tplContent, $this->getData());
        }

        if ($this->getPreview()) {
            if ($default) {
                return $default;
            }

            return true;
        }

        return "Area '$area' not defined";
    }

    public function render($tplContent, $variables = null)
    {
        $this->addData($variables);

        $tplPath = Mage::getSingleton('emaildesign/config')->getTmpPath().DS.microtime(true).rand(1, 10000).'.phtml';

        file_put_contents($tplPath, $tplContent);

        ob_start();
        
        include $tplPath;

        $html = ob_get_clean();

        unlink($tplPath);

        $html = $this->_applyDefaultFilter($html);

        return $html;
    }

    public function _applyDefaultFilter($html)
    {
        $storeId = intval($this->getStoreId());

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId, 'frontend');
        
        $processor = Mage::getModel('core/email_template_filter');
        $processor->setStoreId($storeId);

        if (!method_exists($processor, 'setTemplateProcessor')) {
            return $html;
        }

        $template = Mage::getModel('core/email_template');

        $processor
            ->setTemplateProcessor(array($template, 'getTemplateByConfigPath'))
            ->setIncludeProcessor(array($template, 'getInclude'))
            ->setVariables(array());

        $html = $processor->filter($html);

        $template->setInlineCssFile($processor->getInlineCssFile())
            ->setTemplateType(2);

        $html = $template->getPreparedTemplateText($html);

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $html;
    }
}