<?php

class FreeLunchLabs_CloudFront_Adminhtml_CdnmanagementController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_title($this->__('System'))->_title($this->__('CloudFront CDN Management'));

        $this->loadLayout();
        $this->_setActiveMenu('system');
        $this->renderLayout();
    }

    public function refreshAllAction() {
        Mage::getModel('freelunchlabs_cloudfront/refresh')->refreshDirectory();

        $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('All files on CDN have been refreshed.')
        );

        $this->_redirect('*/*');
    }

    public function refreshMediaAction() {
        Mage::getModel('freelunchlabs_cloudfront/refresh')->refreshDirectory('media');

        $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('All media files on CDN have been refreshed.')
        );

        $this->_redirect('*/*');
    }

    public function refreshSkinAction() {
        Mage::getModel('freelunchlabs_cloudfront/refresh')->refreshDirectory('skin');

        $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('All skin files on CDN have been refreshed.')
        );

        $this->_redirect('*/*');
    }

    public function refreshJsAction() {
        Mage::getModel('freelunchlabs_cloudfront/refresh')->refreshDirectory('js');

        $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('All JavaScript files on CDN have been refreshed.')
        );

        $this->_redirect('*/*');
    }

}