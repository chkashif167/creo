<?php

class FreeLunchLabs_CloudFront_Model_Setup_Observer {

    public function admin_system_config_changed_section_cloudfront() {
        $active = Mage::getStoreConfig('cloudfront/general/active');
        $refresh = Mage::getStoreConfig('cloudfront/general/refresh');
        $distributionId = Mage::getStoreConfig('cloudfront/general/distribution');
        $key = Mage::getStoreConfig('cloudfront/general/key');
        $secret = Mage::getStoreConfig('cloudfront/general/secret');

        if ($active == 1 && $distributionId != "" && $key != "" && $secret != "") {
            Mage::getModel('freelunchlabs_cloudfront/config')->setCloudFrontConfig();
        } elseif ($active == 1) {
            Mage::getSingleton('core/session')->addError('Please check that Distribution ID, AWS Key, and Secret Key are set correctly.');
        } elseif ($active == 0) {
            Mage::getSingleton('core/session')->addSuccess('CloudFront CDN successfully disabled.');
        }

        if ($active == 1 && $refresh == 1) { 
            Mage::getModel('freelunchlabs_cloudfront/refresh')->addRefreshSupport();
        }
    }

}