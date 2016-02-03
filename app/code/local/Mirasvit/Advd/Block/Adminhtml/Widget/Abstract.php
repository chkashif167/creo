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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



abstract class Mirasvit_Advd_Block_Adminhtml_Widget_Abstract extends Mage_Adminhtml_Block_Widget
{
    protected $form = null;

    abstract function getGroup();

    abstract function getName();

    abstract function prepareOptions();

    public function isEnabled()
    {
        return true;
    }

    public function getConfigurationForm()
    {
        $this->form = new Varien_Data_Form();

        $this->form->addField(
            'widget',
            'select',
            array(
                'name'   => 'widget',
                'label'  => Mage::helper('advr')->__('Widget'),
                'values' => Mage::getSingleton('advd/system_config_source_widget')->toOptionArray(true),
                'value'  => $this->getCode(),
                'class'  => 'UI-WIDGET-SELECTOR',
            )
        );

        $this->form->addField(
            'title',
            'text',
            array(
                'name'  => 'title',
                'label' => Mage::helper('advr')->__('Title'),
                'value' => $this->getParam('title', $this->getTitle())
            )
        );

        $this->prepareOptions();

        $this->form->addField(
            'buttons',
            'note',
            array(
                'name'  => 'buttons',
                'label' => '',
                'text'  => '
                    <button class="UI-SAVE scalable save"><span><span>Save</span></span></button>
                    &nbsp;
                    <button class="UI-CANCEL scalable cancel"><span><span>Cancel</span></span></button>',
            )
        );

        return $this->form->getHtml();
    }

    public function getCode()
    {
        return strtolower(str_replace('Mirasvit_Advd_Block_', 'advd/', get_class($this)));
    }

    public function describe()
    {
        $result = array(
            'title'         => $this->getTitle(),
            'configuration' => $this->getConfigurationForm(),
        );

        return $result;
    }

    public function getParams()
    {
        return new Varien_Object($this->getData('params'));
    }

    public function getParam($key, $default = false)
    {
        if ($key == 'store_ids') {
            return array_filter(explode(',', Mage::app()->getRequest()->getParam('store_ids')));
        }

        $params = $this->getData('params');

        if (isset($params[$key])) {
            return $params[$key];
        }

        return $default;
    }

    public function getWidgetTitle()
    {
        return $this->getParam('title');
    }
}
