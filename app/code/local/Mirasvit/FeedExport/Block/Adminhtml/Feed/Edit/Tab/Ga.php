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


class Mirasvit_FeedExport_Block_Adminhtml_Feed_Edit_Tab_Ga extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');
        $form  = new Varien_Data_Form();
        $form->setFieldNameSuffix('feed');
        $this->setForm($form);

        $general = $form->addFieldset('general', array('legend' => __('Google Analytics')));

        $general->addField('ga_source', 'text', array(
            'name'  => 'ga_source',
            'label' => __('Campaign Source'),
            'value' => $model->getGaSource(),
            'note'  => Mage::helper('feedexport/help')->field('ga_source'),
        ));

        $general->addField('ga_medium', 'text', array(
            'name'  => 'ga_medium',
            'label' => __('Campaign Medium'),
            'value' => $model->getGaMedium(),
            'note'  => Mage::helper('feedexport/help')->field('ga_medium'),
        ));

        $general->addField('ga_name', 'text', array(
            'name'  => 'ga_name',
            'label' => __('Campaign Name'),
            'value' => $model->getGaName(),
            'note'  => Mage::helper('feedexport/help')->field('ga_name'),
        ));

        $general->addField('ga_term', 'text', array(
            'name'  => 'ga_term',
            'label' => __('Campaign Term'),
            'value' => $model->getGaTerm(),
            'note'  => Mage::helper('feedexport/help')->field('ga_term'),
        ));

        $general->addField('ga_content', 'text', array(
            'name'  => 'ga_content',
            'label' => __('Campaign Content'),
            'value' => $model->getGaContent(),
            'note'  => Mage::helper('feedexport/help')->field('ga_content'),
        ));

        return parent::_prepareForm();
    }
}