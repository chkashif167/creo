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



class Mirasvit_Advr_Adminhtml_Advr_OrderController extends Mirasvit_Advr_Controller_Report
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_title($this->__('Advanced Reports'))
            ->_title($this->__('Sales'));

        parent::_initAction();

        return $this;
    }

    public function ordersAction()
    {
        $this->_initAction()
            ->_title($this->__('Sales'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_orders'))
            ->_processActions()
            ->renderLayout();
    }

    public function plainAction()
    {
        $this->_initAction()
            ->_title($this->__('Sales'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_plain'))
            ->_processActions()
            ->renderLayout();
    }

    public function hourAction()
    {
        $this->_initAction()
            ->_title($this->__('By Hour'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_hour'))
            ->_processActions()
            ->renderLayout();
    }

    public function dayAction()
    {
        $this->_initAction()
            ->_title($this->__('By Day of Week'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_day'))
            ->_processActions()
            ->renderLayout();
    }

    public function countryAction()
    {
        $this->_initAction()
            ->_title($this->__('By Country'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_country'))
            ->_processActions()
            ->renderLayout();
    }

    public function paymentTypeAction()
    {
        $this->_initAction()
            ->_title($this->__('By Payment Type'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_paymentType'))
            ->_processActions()
            ->renderLayout();
    }

    public function customerGroupAction()
    {
        $this->_initAction()
            ->_title($this->__('Sales By Customer Group'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_customerGroup'))
            ->_processActions()
            ->renderLayout();
    }

    public function couponAction()
    {
        $this->_initAction()
            ->_title($this->__('Sales By Coupon'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_coupon'))
            ->_processActions()
            ->renderLayout();
    }

    public function newVsReturningAction()
    {
        $this->_initAction()
            ->_title($this->__('New vs Returning Customers'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_newVsReturning'))
            ->_processActions()
            ->renderLayout();
    }

    public function customerAction()
    {
        $this->_initAction()
            ->_title($this->__('Sales by Customer'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_customer'))
            ->_processActions()
            ->renderLayout();
    }

    public function categoryAction()
    {
        $this->_initAction()
            ->_title($this->__('Sales by Category'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_category'))
            ->_processActions()
            ->renderLayout();
    }

    public function shippingtimeAction()
    {
        $this->_initAction()
            ->_title($this->__('Average Shipping Time'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_shippingTime'))
            ->_processActions()
            ->renderLayout();
    }

    public function geoAction()
    {
        $this->_initAction()
            ->_title($this->__('Sales by Geo-data'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_geo'))
            ->_processActions()
            ->renderLayout();
    }

    public function shippingmethodAction()
    {
        $this->_initAction()
            ->_title($this->__('Sales by Shipping Method'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_order_shippingMethod'))
            ->_processActions()
            ->renderLayout();
    }

    protected function _isAllowed()
    {
        return (bool)Mage::getSingleton('admin/session')->isAllowed('advr/order')
            || Mage::getSingleton('admin/session')->isAllowed('report/advr/order');
    }
}
