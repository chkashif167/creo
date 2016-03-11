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


class Mirasvit_FeedExport_Block_Adminhtml_Rule_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');
        $form  = new Varien_Data_Form();
        $form->setFieldNameSuffix('data');
        $this->setForm($form);

        $general = $form->addFieldset('general', array('legend' => __('General Information')));

        if ($model->getId()) {
            $general->addField('rule_id', 'hidden', array(
                'name'  => 'rule_id',
                'value' => $model->getId(),
            ));
        }

        $general->addField('name', 'text', array(
            'label'    => __('Name'),
            'required' => true,
            'name'     => 'name',
            'value'    => $model->getName()
        ));

        $general->addField('type', 'select', array(
            'label'    => __('Filter Type'),
            'required' => true,
            'name'     => 'type',
            'value'    => $model->getType(),
            'values'   => Mage::getSingleton('feedexport/system_config_source_ruleType')->toOptionArray(),
            'disabled' => $model->getId() ? true : false,
        ));

        $general->addField('is_active', 'select', array(
            'label'    => __('Is Active'),
            'required' => true,
            'name'     => 'is_active',
            'value'    => $model->getIsActive(),
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $collection = Mage::getModel('feedexport/feed')->getCollection();

        if ($collection->count()) {
            $feeds = $form->addFieldset('feeds_fieldset', array('legend' => __('Feeds')));

            foreach ($collection as $feed) {
                $feeds->addField('feed'.$feed->getId(), 'checkbox', array(
                    'label'    => $feed->getName(),
                    'required' => false,
                    'name'     => 'feed_ids['.$feed->getId().']',
                    'checked'  => is_array($model->getFeedIds()) ? in_array($feed->getId(), $model->getFeedIds()) : false,
                ));
            }
        }

        Mage::dispatchEvent('adminhtml_promo_catalog_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }
}
