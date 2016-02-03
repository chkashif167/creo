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


class Mirasvit_Email_Block_Adminhtml_Trigger_Edit_Tab_Ga extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');
        $form  = new Varien_Data_Form();
        $this->setForm($form);

        $ga = $form->addFieldset('ga', array('legend' => __('Google Analytics Campaign')));

        $ga->addField('ga_source', 'text', array(
            'label'    => __('Campaign Source'),
            'required' => false,
            'name'     => 'ga_source',
            'value'    => $model->getGaSource(),
            'note'     => 'Required (referrer: email, follow-up-email, newsletter)'
        ));

        $ga->addField('ga_medium', 'text', array(
            'label'    => __('Campaign Medium'),
            'required' => false,
            'name'     => 'ga_medium',
            'value'    => $model->getGaMedium(),
            'note'     => 'Required (marketing medium: cpc, banner, email)'
        ));

        $ga->addField('ga_name', 'text', array(
            'label'    => __('Campaign Name'),
            'required' => false,
            'name'     => 'ga_name',
            'value'    => $model->getGaName(),
            'note'     => 'Required (product, promo code, or slogan)'
        ));

        $ga->addField('ga_term', 'text', array(
            'label'    => __('Campaign Term'),
            'required' => false,
            'name'     => 'ga_term',
            'value'    => $model->getGaTerm(),
            'note'     => 'Optional'
        ));

        $ga->addField('ga_content', 'text', array(
            'label'    => __('Campaign Content'),
            'required' => false,
            'name'     => 'ga_content',
            'value'    => $model->getGaContent(),
            'note'     => 'Optional'
        ));


        return parent::_prepareForm();
    }
}
