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



class Mirasvit_Advd_Block_Adminhtml_Notification_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');

        $form = new Varien_Data_Form(
            array(
                'id'      => 'edit_form',
                'action'  => $this->getUrl('*/*/save', array('id' => $model->getId())),
                'method'  => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $general = $form->addFieldset(
            'general',
            array(
                'legend' => Mage::helper('advd')->__('Email'),
                'class'  => 'fieldset-wide'
            )
        );

        if ($model->getId()) {
            $general->addField('email_id', 'hidden', array(
                'name'  => 'email_id',
                'value' => $model->getId(),
            ));
        }

        $general->addField('is_active', 'select', array(
            'label'    => Mage::helper('advd')->__('Is Active'),
            'required' => true,
            'name'     => 'is_active',
            'value'    => $model->getIsActive(),
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $general->addField('email_subject', 'text', array(
            'label'    => Mage::helper('advd')->__('Email Subject'),
            'required' => true,
            'name'     => 'email_subject',
            'value'    => $model->getEmailSubject()
        ));

        $general->addField('recipient_email', 'text', array(
            'label'    => Mage::helper('advd')->__('Email To'),
            'required' => true,
            'name'     => 'recipient_email',
            'value'    => $model->getRecipientEmail(),
            'note'     => Mage::helper('advd')->__('Comma-separated')
        ));

        $general->addField('email_widgets', 'multiselect', array(
            'label'    => Mage::helper('advd')->__('Widgets'),
            'required' => true,
            'name'     => 'email_widgets',
            'value'    => $model->getEmailWidgets(),
            'values'   => $model->getAllWidgets(),
        ));

        $schedule = $form->addFieldset('schedule', array(
            'legend' => Mage::helper('advd')->__('Schedule'),
            'class'  => 'fieldset-wide'
        ));

        $cronStatus = Mage::helper('mstcore/cron')->checkCronStatus(
            false,
            false,
            'Cron job is required for send reports by schedule.'
        );
        if ($cronStatus !== true) {
            $schedule->addField('cronjob_status', 'note', array(
                'name'  => 'cronjob_status',
                'label' => Mage::helper('advd')->__('Cron job'),
                'note'  => $cronStatus,
            ));
        }

        $schedule->addField('current_time', 'label', array(
            'label' => Mage::helper('advd')->__('Current Time'),
            'name'  => 'current_time',
            'value' => Mage::getSingleton('core/date')->date('M, d Y h:i A'),
        ));

        $ts = strtotime($model->getSentAt());
        $schedule->addField('last_sent', 'label', array(
            'label' => Mage::helper('advd')->__('Last Sent'),
            'value' => $ts > 0 ? Mage::getSingleton('core/date')->date('M, d Y h:i A', $ts) : '-',
        ));

        $schedule->addField('schedule_day', 'multiselect', array(
            'label'    => Mage::helper('advd')->__('Day'),
            'required' => true,
            'name'     => 'schedule_day',
            'values'   => Mage::getSingleton('advd/system_config_source_day')->toOptionArray(),
            'value'    => $model->getScheduleDay()
        ));

        $schedule->addField('schedule_time', 'multiselect', array(
            'label'    => Mage::helper('advd')->__('Time'),
            'required' => true,
            'name'     => 'schedule_time',
            'values'   => Mage::getSingleton('advd/system_config_source_time')->toOptionArray(),
            'value'    => $model->getScheduleTime()
        ));

        return parent::_prepareForm();
    }
}
