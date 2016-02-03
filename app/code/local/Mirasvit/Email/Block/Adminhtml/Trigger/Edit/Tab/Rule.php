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


class Mirasvit_Email_Block_Adminhtml_Trigger_Edit_Tab_Rule extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return Mage::helper('email')->__('Rules');
    }

    public function getTabTitle()
    {
        return Mage::helper('email')->__('Rules');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');

        $form = new Varien_Data_Form();

        $runRule = $model->getRunRule();
        $form->setHtmlIdPrefix('rule_');
        $renderer = Mage::app()->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl(Mage::getModel('adminhtml/url')->getUrl('*/*/newConditionHtml/form/rule_run_fieldset'));

        $runFieldset = $form->addFieldset('run_fieldset',
                array('legend' => Mage::helper('email')->__('Send emails only if the following conditions are met')))
            ->setRenderer($renderer);

        $runCond = new Mage_Rule_Block_Conditions();
        $runFieldset->addField('run_conditions', 'text', array(
            'name'     => 'run_conditions',
            'label'    => Mage::helper('email')->__('Rules'),
            'title'    => Mage::helper('email')->__('Rules'),
            'required' => true,
        ))->setRenderer($runCond)->setRule($runRule);

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}

