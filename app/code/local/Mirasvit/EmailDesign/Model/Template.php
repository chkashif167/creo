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


class Mirasvit_EmailDesign_Model_Template extends Mage_Core_Model_Abstract
{
    protected $_areas  = null;
    protected $_design = null;

    protected function _construct()
    {
        $this->_init('emaildesign/template');
    }

    public function getAreas()
    {
        if ($this->_areas == null) {
            if ($this->getDesign()) {
                $this->_areas = $this->getDesign()->getAreas();
            } else {
                $this->_areas = array('content' => 'Content');
            }
        }

        return $this->_areas;
    }

    public function setDesign($design)
    {
        $this->_design = $design;

        return $this;
    }

    public function getDesign()
    {
        if ($this->_design == null && $this->getDesignId()) {
            $this->_design = Mage::getModel('emaildesign/design')->load($this->getDesignId());
        }

        return $this->_design;
    }

    public function getAreaContent($code)
    {
        $areas = $this->getAreasContent();
        if (isset($areas[$code])) {
            return $areas[$code];
        }

        return false;
    }

    public function setAreaContent($code, $content)
    {
        $areas = $this->getAreasContent();
        $areas[$code] = $content;
        $this->setAreasContent($areas);

        return $this;
    }

    public function getPreviewSubject()
    {
        $variables = Mage::helper('email/event')->getRandomEventArgs();
        $variables['preview'] = true;

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($variables['store_id']);

        $result = $this->getProcessedTemplateSubject($variables);

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $result;
    }

    public function getPreviewContent()
    {
        $variables = Mage::helper('email/event')->getRandomEventArgs();
        $variables['preview'] = true;

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($variables['store_id']);

        $result = $this->getProcessedTemplate($variables);

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        if ($this->getDesign()->getTemplateType() == Mirasvit_EmailDesign_Model_Design::TEMPLATE_TYPE_TEXT) {
            $result = nl2br($result);
        }

        return $result;
    }

    public function getProcessedTemplate($variables = null)
    {
        $tpl = $this->getDesign()->getTemplate();
        
        $result = $this->_render($tpl, $variables);

        if ($this->getDesign()->getTemplateType() == Mirasvit_EmailDesign_Model_Design::TEMPLATE_TYPE_HTML) {
            $result = Mage::helper('emaildesign')->styleHtml($result);
        }

        return $result;
    }

    public function getProcessedTemplateSubject($variables = null)
    {
        return $this->_render($this->getSubject(), $variables);
    }

    protected function _render($tpl, $variables = null)
    { 
        if (!is_array($variables)) {
            $variables = array();
        }

        foreach ($this->getAreasContent() as $area => $content) {
            $variables['area_'.$area] = $content;
        }

        $block = Mage::app()->getLayout()->createBlock('emaildesign/template');

        $result = $block->render($tpl, $variables);

        return $result;
    }

    public function export()
    {
        $this->setAreasContent64(base64_encode(serialize($this->getAreasContent())));
        $this->setDesignTitle($this->getDesign()->getTitle());

        $xml = $this->toXml(array('title', 'description', 'subject', 'design_title', 'areas_content64'));

        $path = Mage::getSingleton('emaildesign/config')->getTemplatePath().DS.$this->getTitle().'.xml';

        file_put_contents($path, $xml);

        return $path;
    }

    public function import($path)
    {
        $content   = file_get_contents($path);
        $xml       = new Varien_Simplexml_Element($content);
        $template  = $xml->asArray();

        $template['areas_content'] = base64_decode($template['areas_content64']);

        $model = $this->getCollection()
            ->addFieldToFilter('title', $template['title'])
            ->getFirstItem();
        $model->addData($template);

        $design = Mage::getModel('emaildesign/design')->getCollection()
            ->addFieldToFilter('title', $template['design_title'])
            ->getFirstItem();

        $model->setDesignId($design->getId())
            ->save();

        return $model;
    }
}