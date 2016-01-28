<?php

class FreeLunchLabs_CloudFront_Block_Adminhtml_Cdnmanagement extends Mage_Adminhtml_Block_Template {

    function isRefreshEnabled() {
        if (Mage::getStoreConfig('cloudfront/general/refresh') && Mage::getStoreConfig('cloudfront/general/active')) {
            return true;
        } else {
            return false;
        }
    }

    function getRefreshAllLink() {
        return $this->getUrl('*/*/refreshAll');
    }

    function getMediaRefreshLink() {
        return $this->getUrl('*/*/refreshMedia');
    }

    function getSkinRefreshLink() {
        return $this->getUrl('*/*/refreshSkin');
    }

    function getJsRefreshLink() {
        return $this->getUrl('*/*/refreshJs');
    }

    function isMediaCdnEnabled() {
        if (Mage::getStoreConfig('cloudfront/media/media')) {
            return true;
        } else {
            return false;
        }
    }

    function isSkinCdnEnabled() {
        if (Mage::getStoreConfig('cloudfront/skin/skin')) {
            return true;
        } else {
            return false;
        }
    }

    function isJsCdnEnabled() {
        if (Mage::getStoreConfig('cloudfront/js/js')) {
            return true;
        } else {
            return false;
        }
    }

}
