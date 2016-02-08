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


class Mirasvit_Email_Block_Adminhtml_Trigger_Edit_Tab_Renderer_Chain implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return Mage::app()->getLayout()
            ->createBlock('adminhtml/template')
            ->setParent($this)
            ->setTemplate('mst_email/trigger/edit/tab/renderer/chain.phtml')
            ->setChainCollection($this->getChainCollection())
            ->toHtml();   
    }

    public function getChainCollection()
    {
        $collection = Mage::registry('current_model')->getChainCollection();
        if ($collection->count() == 0) {
            $collection->addItem(Mage::getModel('email/trigger_chain')->setId(0));
        }

        return $collection;
    }

    public function getCouponFieldset($model)
    {
        $form  = new Varien_Data_Form();
        $prefix = 'chain['.$model->getId().'][';

        $fieldset = $form->addFieldset('fieldset', array('legend' => Mage::helper('email')->__('Coupons')));
    
        $fieldset->addField('coupon_enabled', 'select', array(
            'label'    => Mage::helper('email')->__('Include coupon in email'),
            'required' => false,
            'name'     => $prefix.'coupon_enabled]',
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'value'    => $model->getCouponEnabled(),
        ));

        $fieldset->addField('coupon_sales_rule_id', 'select', array(
            'label'    => Mage::helper('email')->__('Shopping Cart Price Rule'),
            'required' => false,
            'name'     => $prefix.'coupon_sales_rule_id]',
            'values'   => Mage::getSingleton('email/system_source_salesRule')->toOptionArray(),
            'value'    => $model->getCouponSalesRuleId(),
        ));

        $fieldset->addField('coupon_expires_days', 'text', array(
            'label'    => Mage::helper('email')->__('Coupon expires after, days'),
            'required' => false,
            'name'     => $prefix.'coupon_expires_days]',
            'value'    => $model->getCouponExpiresDays(),
        ));

        return $form->toHtml();
    }

    public function getCrossSellFieldset($model)
    {
        $form  = new Varien_Data_Form();
        $prefix = 'chain['.$model->getId().'][';

        $fieldset = $form->addFieldset('fieldset', array('legend' => Mage::helper('email')->__('Cross-sells')));
    
        $fieldset->addField('cross_sells_enabled', 'select', array(
            'label'    => Mage::helper('email')->__('Include cross-sells in email'),
            'required' => false,
            'name'     => $prefix.'cross_sells_enabled]',
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'value'    => $model->getCrossSellsEnabled(),
        ));

        $fieldset->addField('cross_sells_type_id', 'select', array(
            'label'    => Mage::helper('email')->__('Cross-sells source'),
            'required' => false,
            'name'     => $prefix.'cross_sells_type_id]',
            'values'   => Mage::getSingleton('email/system_source_crossSell')->toOptionArray(),
            'value'    => $model->getCrossSellsTypeId(),
        ));

        return $form->toHtml();
    }

    public function getExcludeDaysFieldset($model)
    {
        $form  = new Varien_Data_Form();
        $prefix = 'chain['.$model->getId().'][';

        $fieldset = $form->addFieldset('fieldset', array('legend' => Mage::helper('email')->__('Options')));
        $fieldset->addField('exclude_days', 'multiselect', array(
            'label'    => Mage::helper('email')->__('Excluded Weekdays'),
            'required' => false,
            'name'     => $prefix.'exclude_days]',
            'value'    => $model->getExcludeDays(),
            'values'   => Mage::getModel('adminhtml/system_config_source_locale_weekdays')->toOptionArray()
        ));

        return $form->toHtml();
    }
}