<?php

class FreeLunchLabs_CloudFront_Model_Refresh extends Varien_Object {

    public $cdn_rewrite_directory = "cdn";
    private $cdn_directories = array('media', 'skin', 'js');

    function addRefreshSupport() {
        $this->getAllKeys();
        $this->writeRewriteFile();
    }

    function refreshDirectory($directory = null) {
        if (is_null($directory)) {
            $directories = $this->cdn_directories;
        } else {
            $directories = array(0 => $directory);
        }

        foreach ($directories as $cdn_directory) {
            $this->setKey($cdn_directory);
        }

        Mage::getModel('freelunchlabs_cloudfront/config')->setCloudFrontConfig();
    }

    function writeRewriteFile() {
        $adapter = Mage::getModel('freelunchlabs_cloudfront/refreshadapters_apache');
        $base_dir = Mage::getBaseDir() . DS;
        $file = new Varien_Io_File();

        try {
            if ($file->cd($base_dir) && $file->checkAndCreateFolder($this->cdn_rewrite_directory, 0755)) {
                if ($file->cd($base_dir . $this->cdn_rewrite_directory)) {
                    if(!$file->write($adapter->filename, $adapter->buildFileContents(), 0644)){
                        throw new Exception("Could not write .htaccess to: " . $file->pwd());
                    }
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addWarning('Configuration saved but there was an error creating the .htaccess file: ' . $e->getMessage());
        }
    }

    public function isDirectoryWriteable() {
        $base_dir = Mage::getBaseDir() . DS;
        $file = new Varien_Io_File();

        if ($file->isWriteable($base_dir)) {
            return true;
        } else {
            return false;
        }
    }

    function getRefreshURL($directory) {
        if (Mage::getStoreConfig('cloudfront/general/refresh') == 1) {
            return $this->cdn_rewrite_directory . "/" . $this->getKey($directory) . "/";
        } else {
            return "";
        }
    }

    function getAllKeys() {
        $keys = array();

        foreach ($this->cdn_directories as $directory) {
            $keys[$directory] = $this->getKey($directory);
        }

        return $keys;
    }

    function getKey($directory) {
        Mage::app()->getStore()->resetConfig();
        $key = Mage::getStoreConfig('cloudfront/general/refresh_' . $directory . '_key');

        if ($key) {
            return $key;
        } else {
            return $this->setKey($directory);
        }
    }

    function setKey($directory) {
        $key = rand(0, 999999);
        Mage::getModel('core/config')->saveConfig('cloudfront/general/refresh_' . $directory . '_key', $key);
        return $key;
    }

    function loadProxiedURL($url) {
        $header[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i686; pl-PL; rv:1.9.0.2) Gecko/2008092313 Ubuntu/9.25 (jaunty) Firefox/3.8');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, Mage::getStoreConfig('web/unsecure/base_link_url'));
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);

        $data = curl_exec($ch);

        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        curl_close($ch);

        return array(
            'data' => $data,
            'http_status' => $http_status,
            'content_type' => $contentType
        );
    }

    public function testRefreshSupport() {
        $url = Mage::getStoreConfig('web/unsecure/base_link_url') . $this->$cdn_rewrite_directory . $this->getKey('skin') . Mage::getStoreConfig('cloudfront/general/testFile');

        $content = $this->loadProxiedURL($url);

        if ($content['http_status'] == 200) {
            return true;
        } else {
            return false;
        }
    }
}