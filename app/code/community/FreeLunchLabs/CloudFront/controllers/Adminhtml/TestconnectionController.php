<?php

class FreeLunchLabs_CloudFront_Adminhtml_TestconnectionController extends Mage_Adminhtml_Controller_Action {

    public function pingAction() {
        $key = Mage::app()->getRequest()->getParam('awskey');
        $secret = Mage::app()->getRequest()->getParam('awssecret');
        $origin = Mage::app()->getRequest()->getParam('awsorigin');

        $cloudfront = Mage::getModel('freelunchlabs_cloudfront/cloudfront', array('key' => $key, 'secret' => $secret));
        $cloudfront->setOrigin($origin);
        $result = $cloudfront->createDistribution();

        if ($result['status']) {
            echo json_encode(array('status' => 'success', 'message' => 'Distribution Successfully Created', 'distribution' => $result['distribution']));
        } else {
            echo json_encode(array('status' => 'error', 'message' => $result['message'], 'distribution' => $result['distribution']));
        }
    }

}