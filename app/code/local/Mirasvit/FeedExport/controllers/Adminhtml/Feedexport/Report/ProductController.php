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



class Mirasvit_FeedExport_Adminhtml_Feedexport_Report_ProductController extends Mirasvit_FeedExport_Controller_Adminhtml_Report
{
    public function indexAction()
    {
        $this->_showRefreshRecent();
        $this->_title($this->__('Feed report by products'));
        $this->_initAction()
            ->_setActiveMenu('catalog/feedexport');

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_product.grid');
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
            Mage::getResourceModel('feedexport/performance_aggregated')->aggregate();
            Mage::getSingleton('adminhtml/session')->addSuccess(__('Statistics have been updated.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(__('Unable to refresh statistics.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*');

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/feedexport/feedexport_report/feedexport_report_product');
    }
}
