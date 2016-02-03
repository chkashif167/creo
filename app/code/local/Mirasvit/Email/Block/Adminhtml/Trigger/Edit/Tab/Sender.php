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


class Mirasvit_Email_Block_Adminhtml_Trigger_Edit_Tab_Sender extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');
        $form  = new Varien_Data_Form();
        $this->setForm($form);

        $sender = $form->addFieldset('sender', array('legend' => __('Sender Details')));

        $sender->addField('sender_email', 'text', array(
            'label'    => __('Sender Email'),
            'required' => false,
            'name'     => 'sender_email',
            'value'    => $model->getData('sender_email'),
            'note'     => 'If not defined, sender email from the general settings is used by default'
        ));

        $sender->addField('sender_name', 'text', array(
            'label'    => __('Sender Name'),
            'required' => false,
            'name'     => 'sender_name',
            'value'    => $model->getData('sender_name'),
            'note'     => 'If not defined, sender name from the general settings is used by default'
        ));

        $sender->addField('copy_email', 'text', array(
            'label'    => __('Send copy to email'),
            'required' => false,
            'name'     => 'copy_email',
            'value'    => $model->getCopyEmail(),
            'note'     => 'These addresses will be added to the BCC. <br>
                Separate e-mails by commas'
        ));

        return parent::_prepareForm();
    }
}
