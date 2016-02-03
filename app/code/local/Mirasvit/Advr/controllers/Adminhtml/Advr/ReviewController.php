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



class Mirasvit_Advr_Adminhtml_Advr_ReviewController extends Mirasvit_Advr_Controller_Report
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_title($this->__('Advanced Reports'))
            ->_title($this->__('Reviews'));

        parent::_initAction();

        return $this;
    }

    public function reviewAction()
    {
        $this->_initAction()
            ->_title($this->__('Reviews'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_review_review'))
            ->_processActions()
            ->renderLayout();
    }

    protected function _isAllowed()
    {
        return (bool)Mage::getSingleton('admin/session')->isAllowed('advr/review')
            || Mage::getSingleton('admin/session')->isAllowed('report/advr/review');
    }
}
