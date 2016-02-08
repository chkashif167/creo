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


class Mirasvit_Email_Block_Adminhtml_Trigger_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');
        $form  = new Varien_Data_Form();
        $this->setForm($form);

        $general = $form->addFieldset('general', array('legend' => Mage::helper('email')->__('General Information')));

        if ($model->getId()) {
            $general->addField('feed_id', 'hidden', array(
                'name'      => 'feed_id',
                'value'     => $model->getId(),
            ));
        }

        $general->addField('title', 'text', array(
            'label'    => Mage::helper('email')->__('Name'),
            'required' => true,
            'name'     => 'title',
            'value'    => $model->getTitle()
        ));

        $general->addField('description', 'textarea', array(
            'label'    => Mage::helper('email')->__('Description'),
            'required' => false,
            'name'     => 'description',
            'value'    => $model->getDescription(),
            'style'    => 'height:50px'
        ));
        $general->addField('is_trigger_sandbox_active', 'select', array(
            'label'    => Mage::helper('email')->__('Is email redirection active'),
            'required' => false,
            'name'     => 'is_trigger_sandbox_active',
            'value'    => $model->getIsTriggerSandboxActive(),
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));
        $general->addField('trigger_sandbox_email', 'text', array(
            'label'    => Mage::helper('email')->__('Send only to email'),
            'required' => false,
            'name'     => 'trigger_sandbox_email',
            'value'    => $model->getTriggerSandboxEmail(),
            'note'     => 'If not defined messages will be sent to the clients'
        ));
        $general->addField('is_active', 'select', array(
            'label'    => Mage::helper('email')->__('Is Active'),
            'required' => true,
            'name'     => 'is_active',
            'value'    => $model->getIsActive(),
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $general->addField('active_from', 'date', array(
            'label'        => Mage::helper('email')->__('Active from'),
            'name'         => 'active_from',
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'format'       => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'time'         => true,
            'required'     => false,
            'value'        => $model->getActiveFrom() == '0000-00-00 00:00:00' ? '' : $model->getActiveFrom(),
        ));

        $general->addField('active_to', 'date', array(
            'label'        => Mage::helper('email')->__('Active to'),
            'name'         => 'active_to',
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'format'       => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'time'         => true,
            'required'     => false,
            'value'        => $model->getActiveTo() == '0000-00-00 00:00:00' ? '' : $model->getActiveTo(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $general->addField('store_ids', 'multiselect', array(
                'label'    => Mage::helper('email')->__('Store View'),
                'required' => true,
                'name'     => 'store_ids',
                'value'    => $model->getStoreIds(),
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        } else {
            $general->addField('store_id', 'hidden', array(
                'name'  => 'store_id',
                'value' => Mage::app()->getStore(true)->getId()
            ));
        }

        // $general->addField('trigger_type', 'radios', array(
        //     'label'    => Mage::helper('email')->__('Trigger by'),
        //     'required' => true,
        //     'name'     => 'trigger_type',
        //     'value'    => $model->getIsActive(),
        //     'values'   => array(
        //         array(
        //             'label' => ' Event',
        //             'value' => 'event'
        //         ),
        //         array(
        //             'label' => ' Schedule',
        //             'value' => 'schedule'
        //         ),
        //     )
        // ));

        $general->addField('event', 'select', array(
            'label'    => Mage::helper('email')->__('Event'),
            'required' => true,
            'name'     => 'event',
            'value'    => $model->getEvent(),
            'values'   => Mage::getSingleton('email/system_source_event')->toOptionArray(),
        ));

        $general->addField('cancellation_event', 'multiselect', array(
            'label'    => Mage::helper('email')->__('Cancellation Event'),
            'required' => false,
            'name'     => 'cancellation_event',
            'value'    => $model->getCancellationEvent(),
            'values'   => Mage::getSingleton('email/system_source_event')->toOptionArray(),
        ));


        $chain = $form->addFieldset('chain', array('legend' => Mage::helper('email')->__('Email Chain')));
        $helper = new Mirasvit_Email_Block_Adminhtml_Trigger_Edit_Tab_Renderer_Chain();
        $chain->setRenderer($helper);

        return parent::_prepareForm();
    }

    protected function _toHtml()
    {
        $dependency_block = $this->getLayout()
            ->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap('is_trigger_sandbox_active', 'is_trigger_sandbox_active')
            ->addFieldMap('trigger_sandbox_email', 'trigger_sandbox_email')
            ->addFieldDependence('trigger_sandbox_email', 'is_trigger_sandbox_active', 1);

        return parent::_toHtml() . $dependency_block->toHtml();
    }
}
