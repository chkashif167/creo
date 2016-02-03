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


class Mirasvit_EmailReport_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Widget_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_dashboard_index';
        $this->_blockGroup = 'emailreport';
        $this->_headerText = Mage::helper('emailreport')->__('Statistics');

        parent::__construct();

        $this->addButton('filter_form_submit', array(
            'label'   => Mage::helper('emailreport')->__('Show Report'),
            'onclick' => 'filterFormSubmit()'
        ));

        $this->_addButton('check_events', array(
            'label'   => Mage::helper('email')->__('Refresh Report Data'),
            'onclick' => "window.location.href='".Mage::helper("adminhtml")->getUrl('*/*/aggregate')."'",
        ), -100);
    }
}