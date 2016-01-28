<?php

class FreeLunchLabs_CloudFront_Model_Observer {

    function catalog_product_gallery_upload_image_after() {
        if (Mage::getStoreConfig('cloudfront/general/refresh') && Mage::getStoreConfig('cloudfront/general/active') && Mage::getStoreConfig('cloudfront/media/auto_refresh_media')) {
            Mage::getModel('freelunchlabs_cloudfront/refresh')->refreshDirectory('media');
        }
    }

}