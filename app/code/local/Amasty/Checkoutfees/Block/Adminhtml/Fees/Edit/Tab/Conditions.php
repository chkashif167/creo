<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Block_Adminhtml_Fees_Edit_Tab_Conditions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('salesrule')->__('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('salesrule')->__('Conditions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('amcheckoutfees_fees');
        $data = $model->getData();

        $form     = new Varien_Data_Form();
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                        ->setTemplate('promo/fieldset.phtml')
                        ->setNewChildUrl($this->getUrl('adminhtml/amcheckoutfees_checkoutFees/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('rule_conditions_fieldset', array(
                'legend' => Mage::helper('salesrule')->__('Apply the fee only if the following conditions are met')
            )
        )->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
                'name'  => 'conditions',
                'label' => Mage::helper('salesrule')->__('Conditions'),
                'title' => Mage::helper('salesrule')->__('Conditions'),
            )
        )->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
