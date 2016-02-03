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


class Mirasvit_Email_Block_Adminhtml_Trigger_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Trigger Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'   => __('General Information'),
            'title'   => __('General Information'),
            'content' => $this->getLayout()->createBlock('email/adminhtml_trigger_edit_tab_general')->toHtml(),
        ));

        $this->addTab('rule_section', array(
            'label'   => __('Rules'),
            'title'   => __('Rules'),
            'content' => $this->getLayout()->createBlock('email/adminhtml_trigger_edit_tab_rule')->toHtml(),
        ));

        $this->addTab('sender_section', array(
            'label'   => __('Sender Details'),
            'title'   => __('Sender Details'),
            'content' => $this->getLayout()->createBlock('email/adminhtml_trigger_edit_tab_sender')->toHtml(),
        ));

        $this->addTab('ga_section', array(
            'label'   => __('Google Analytics'),
            'title'   => __('Google Analytics'),
            'content' => $this->getLayout()->createBlock('email/adminhtml_trigger_edit_tab_ga')->toHtml(),
        ));

        $this->addTab('additonal_section', array(
            'label'   => __('Additional'),
            'title'   => __('Additional'),
            'content' => $this->getLayout()->createBlock('email/adminhtml_trigger_edit_tab_additional')->toHtml(),
        ));


        return parent::_beforeToHtml();
    }
}