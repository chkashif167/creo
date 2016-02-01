<?php

class FreeLunchLabs_CloudFront_Model_Store extends Mage_Core_Model_Store {

    public function getBaseUrl($type = self::URL_TYPE_LINK, $secure = null) {
        $cacheKey = $type . '/' . (is_null($secure) ? 'null' : ($secure ? 'true' : 'false'));
        if (!isset($this->_baseUrlCache[$cacheKey])) {
            if ($this->getConfig('cloudfront/general/active') && $this->getConfig('cloudfront/general/deployed') && !$this->isAdmin() && Mage::getDesign()->getArea() != 'adminhtml') {
                $secure = is_null($secure) ? $this->isCurrentlySecure() : (bool) $secure;

                if ($type == self::URL_TYPE_SKIN && $this->getConfig('cloudfront/skin/skin')) {
                    if ($secure) {
                        $url = $this->getConfig('cloudfront/secure/base_skin_url');
                    } else {
                        $url = $this->getConfig('cloudfront/unsecure/base_skin_url');
                    }
                } elseif ($type == self::URL_TYPE_JS && $this->getConfig('cloudfront/js/js')) {
                    if ($secure) {
                        $url = $this->getConfig('cloudfront/secure/base_js_url');
                    } else {
                        $url = $this->getConfig('cloudfront/unsecure/base_js_url');
                    }
                } elseif ($type == self::URL_TYPE_MEDIA && $this->getConfig('cloudfront/media/media')) {
                    if ($secure) {
                        $url = $this->getConfig('cloudfront/secure/base_media_url');
                    } else {
                        $url = $this->getConfig('cloudfront/unsecure/base_media_url');
                    }
                } else {
                    return parent::getBaseUrl($type, $secure);
                }

                if (false !== strpos($url, '{{base_url}}')) {
                    $baseUrl = Mage::getConfig()->substDistroServerVars('{{base_url}}');
                    $url = str_replace('{{base_url}}', $baseUrl, $url);
                }
                
                $this->_baseUrlCache[$cacheKey] = rtrim($url, '/') . '/';
            } else {
                return parent::getBaseUrl($type, $secure);
            }
        }
        
        return $this->_baseUrlCache[$cacheKey];       
    }

}