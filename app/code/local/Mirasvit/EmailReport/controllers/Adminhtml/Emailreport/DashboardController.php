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


class Mirasvit_EmailReport_Adminhtml_Emailreport_DashboardController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('email')
            ->_title(Mage::helper('email')->__('Follow Up Email'), Mage::helper('email')->__('Follow Up Email'))
            ->_title(Mage::helper('email')->__('Statistics'), Mage::helper('email')->__('Statistics'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_title($this->__('Dashboard'));

        $filterBlock      = $this->getLayout()->getBlock('filter_form');
        $gridTriggerBlock = $this->getLayout()->getBlock('grid_trigger');
        $gridPeriodBlock  = $this->getLayout()->getBlock('grid_period');
        $chartBlock  = $this->getLayout()->getBlock('chart');

        $this->_initReportAction(array($filterBlock, $gridTriggerBlock, $gridPeriodBlock, $chartBlock));

        $this->renderLayout();
    }

    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, array('from', 'to'));

        $params = new Varien_Object();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }

        return $this;
    }

    public function aggregateAction()
    {
        Mage::getSingleton('emailreport/aggregated')->aggregateAll();

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('emailreport')->__('Completed'));
        
        $this->_redirect('*/*/index');
    }

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('email/emailreport');
	}
}