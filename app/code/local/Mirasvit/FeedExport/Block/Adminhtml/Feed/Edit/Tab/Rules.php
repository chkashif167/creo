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


class Mirasvit_FeedExport_Block_Adminhtml_Feed_Edit_Tab_Rules extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');
        $form  = new Varien_Data_Form();
        $form->setFieldNameSuffix('feed');
        $this->setForm($form);

       
        $productFieldset = $form->addFieldset('feed_tab_rule_product', array('legend' => __('Product Filters')));

        $collection = Mage::getModel('feedexport/rule')->getCollection()->addTypeAttributeFilter();
        foreach ($collection as $rule) {
            $this->_addRuleToFieldset($rule, $productFieldset, $model);
        }

        $performanceFieldset = $form->addFieldset('feed_tab_rule_performance', array('legend' => __('Performance Filters')));

        $collection = Mage::getModel('feedexport/rule')->getCollection()->addTypePerformanceFilter();
        foreach ($collection as $rule) {
            $this->_addRuleToFieldset($rule, $performanceFieldset, $model);
        }

        return parent::_prepareForm();
    }

    protected function _addRuleToFieldset($rule, $fieldset, $feed)
    {
        $fieldset->addField('rule'.$rule->getId(), 'checkbox', array(
            'label'    => $rule->getName(),
            'name'     => 'rule_ids['.$rule->getId().']',
            'checked'  => is_array($feed->getRuleIds()) ? in_array($rule->getId(), $feed->getRuleIds()) : false,
            'required' => false,
            'note'     => $rule->toString(),
        ));

        return $this;
    }
}
