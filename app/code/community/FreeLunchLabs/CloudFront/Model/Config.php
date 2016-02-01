<?php

class FreeLunchLabs_CloudFront_Model_Config extends Varien_Object {

    private $UnsecureMediaConfigPath = "cloudfront/unsecure/base_media_url";
    private $UnsecureSkinConfigPath = "cloudfront/unsecure/base_skin_url";
    private $UnsecureJsConfigPath = "cloudfront/unsecure/base_js_url";
    private $SecureMediaConfigPath = "cloudfront/secure/base_media_url";
    private $SecureSkinConfigPath = "cloudfront/secure/base_skin_url";
    private $SecureJsConfigPath = "cloudfront/secure/base_js_url";

    const IS_CLOUDFRONT_DISTRIBUTION_DEPLOYED = "cloudfront/general/deployed";

    public function setCloudFrontConfig() {
        $cloudfront = Mage::getModel('freelunchlabs_cloudfront/cloudfront');
        $distribution = $cloudfront->getDistribution();

        if ($distribution['payload']['Status'] == "Deployed" && $distribution['enabled'] == "true") {
            if (Mage::getStoreConfig('cloudfront/media/media')) {
                $this->setMediaConfig($distribution['payload']['DomainName'] . $cloudfront->getOriginPath());
            }

            if (Mage::getStoreConfig('cloudfront/skin/skin')) {
                $this->setSkinConfig($distribution['payload']['DomainName'] . $cloudfront->getOriginPath());
            }

            if (Mage::getStoreConfig('cloudfront/js/js')) {
                $this->setJsConfig($distribution['payload']['DomainName'] . $cloudfront->getOriginPath());
            } 

            $this->setIsDeployedFlag();
            
            if (Mage::getStoreConfig('cloudfront/general/refreshCache')) {
                Mage::getModel('core/cache')->clean("CONFIG");
            }
        } else {
            $this->removedIsDeployedFlag();
            Mage::getSingleton('core/session')->addError('Your Cloud Front distribution is not enabled and deployed yet. Please wait 15 minutes and then try this action again.');
        }
    }
    
    public function setMediaConfig($distribution_url) {
        if ($distribution_url != "") {

            $refresh_support = Mage::getModel('freelunchlabs_cloudfront/refresh');

            $unsecure_media_url = "http://" . $distribution_url . $refresh_support->getRefreshURL('media') . Mage::getStoreConfig('cloudfront/media/media_path');
            Mage::getModel('core/config')->saveConfig($this->UnsecureMediaConfigPath, $unsecure_media_url);

            $secure_media_url = "https://" . $distribution_url . $refresh_support->getRefreshURL('media') . Mage::getStoreConfig('cloudfront/media/media_path');
            Mage::getModel('core/config')->saveConfig($this->SecureMediaConfigPath, $secure_media_url);
        }
    }

    public function setSkinConfig($distribution_url) {
        if ($distribution_url != "") {
            $refresh_support = Mage::getModel('freelunchlabs_cloudfront/refresh');

            $unsecure_skin_url = "http://" . $distribution_url . $refresh_support->getRefreshURL('skin') . Mage::getStoreConfig('cloudfront/skin/skin_path');
            Mage::getModel('core/config')->saveConfig($this->UnsecureSkinConfigPath, $unsecure_skin_url);

            $secure_skin_url = "https://" . $distribution_url . $refresh_support->getRefreshURL('skin') . Mage::getStoreConfig('cloudfront/skin/skin_path');
            Mage::getModel('core/config')->saveConfig($this->SecureSkinConfigPath, $secure_skin_url);
        }
    }

    public function setJsConfig($distribution_url) {
        if ($distribution_url != "") {
            $refresh_support = Mage::getModel('freelunchlabs_cloudfront/refresh');

            $unsecure_js_url = "http://" . $distribution_url . $refresh_support->getRefreshURL('js') . Mage::getStoreConfig('cloudfront/js/js_path');
            Mage::getModel('core/config')->saveConfig($this->UnsecureJsConfigPath, $unsecure_js_url);

            $secure_js_url = "https://" . $distribution_url . $refresh_support->getRefreshURL('js') . Mage::getStoreConfig('cloudfront/js/js_path');
            Mage::getModel('core/config')->saveConfig($this->SecureJsConfigPath, $secure_js_url);
        }
    }
    
    public function setIsDeployedFlag() {
        Mage::getModel('core/config')->saveConfig(self::IS_CLOUDFRONT_DISTRIBUTION_DEPLOYED, 1);
    }
    
    public function removedIsDeployedFlag() {
        Mage::getModel('core/config')->saveConfig(self::IS_CLOUDFRONT_DISTRIBUTION_DEPLOYED, 0);
    }

}