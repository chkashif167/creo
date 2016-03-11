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



class Mirasvit_FeedExport_Adminhtml_Feedexport_ReportController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog')
            ->_addBreadcrumb(__('Feed Export'), __('Feed Export'));

        return $this;
    }

    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, array('from', 'to'));
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
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

    public function indexAction()
    {
        $this->_showRefreshRecent();
        $this->_title($this->__('Feed report'));

        $this->_initAction()
            ->_setActiveMenu('catalog/feedexport');

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_tracker.grid');
        $chartBlock = $this->getLayout()->getBlock('report.chart');
        $chartBlock->setGrid($gridBlock);

        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock,
        ));

        $this->renderLayout();
    }

    public function refreshRecentAction()
    {
        try {
            $currentDate = Mage::app()->getLocale()->date();

            Mage::getResourceModel('feedexport/performance_aggregated')->aggregate();

            Mage::getSingleton('adminhtml/session')->addSuccess(__('Statistics have been updated.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(__('Unable to refresh statistics.'));
            Mage::logException($e);
        }

        $this->_redirect('*/*');

        return $this;
    }

    protected function _showRefreshRecent()
    {
        $directRefreshLink = $this->getUrl('*/feedexport_report/refreshRecent');
        Mage::getSingleton('adminhtml/session')->addNotice(__('To refresh statistics, click <a href="%s">here</a>.',
            $directRefreshLink));

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/feedexport/feedexport_report');
    }
}
