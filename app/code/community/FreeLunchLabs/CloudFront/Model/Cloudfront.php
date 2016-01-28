<?php

class FreeLunchLabs_CloudFront_Model_CloudFront extends Varien_Object {

    protected $_adapter;

    public function __construct($args) {

        if (!isset($args['key']) || !isset($args['secret'])) {
            $args['key'] = Mage::getStoreConfig('cloudfront/general/key');
            $args['secret'] = Mage::getStoreConfig('cloudfront/general/secret');
        }

        $args['certificate_authority'] = true;
        define('AWS_DISABLE_CONFIG_AUTO_DISCOVERY', true);
        
        require_once Mage::getBaseDir('lib') . DS . 'AWS' . DS . 'sdk.class.php';
        $this->_adapter = new AmazonCloudFront($args);
    }

    public function createDistribution() {

        $response = $this->_adapter->create_distribution($this->getOriginUrl(), $this->getCallerReference(), $this->getDistributionOptions());
        $result = $this->processResponse($response);

        return array(
            "status" => $result['status'],
            "message" => $result['message'],
            "distribution" => $result['payload']['Id']
        );
    }

    public function getDistribution() {
        $distributionId = Mage::getStoreConfig('cloudfront/general/distribution');

        $response = $this->_adapter->get_distribution_info($distributionId);
        $result = $this->processResponse($response);

        return $result;
    }

    private function getDistributionOptions() {

        $distributionOptions = array(
            'Comment' => 'Free Lunch Labs / Magento CloudFront',
            'CachingBehavior' => array('MinTTL' => 7200),
            'OriginProtocolPolicy' => 'http-only'
        );

        return $distributionOptions;
    }

    private function getCallerReference() {
        return "FLL_CloudFront_" . time();
    }

    private function getOriginUrlParts() {
        if (is_null($this->getOrigin())) {
            $orginConfig = Mage::getStoreConfig('cloudfront/general/origin');
        } else {
            $orginConfig = $this->getOrigin();
        }

        switch ($orginConfig) {
            case "{{unsecure_base_url}}":
                $orginConfig = Mage::getStoreConfig('web/unsecure/base_url');
                break;
            case "{{secure_base_url}}":
                $orginConfig = Mage::getStoreConfig('web/secure/base_url');
                break;
            case "{{base_url}}":
                $orginConfig = Mage::getStoreConfig('web/unsecure/base_url');
                break;
        }

        return parse_url($orginConfig);
    }

    private function getOriginUrl() {
        $parts = $this->getOriginUrlParts();

        return $parts['scheme'] . "://" . $parts['host'];
    }

    public function getOriginPath() {
        $parts = $this->getOriginUrlParts();

        return $parts['path'];
    }

    private function processResponse($response) {

        $result = array();

        if ($response->isOK()) {
            $result['status'] = true;
            $result['message'] = "";
            $result['payload'] = get_object_vars($response->body);
            $result['enabled'] = (string) $response->body->DistributionConfig->Enabled;
        } else {
            $result['status'] = false;
            $result['message'] = (string) $response->body->Error->Message;
            $result['payload'] = "";
            $result['enabled'] = "";
        }

        return $result;
    }

}