<?php
/**
* Fetchr.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* It is also available through the world-wide-web at this URL:
* https://fetchr.zendesk.com/hc/en-us/categories/200522821-Downloads
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to ws@fetchr.us so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade Fetchr Magento Extension to newer
* versions in the future. If you wish to customize Fetchr Magento Extension (Fetchr Shipping) for your
* needs please refer to http://www.fetchr.us for more information.
*
* @author     Danish Kamal
* @copyright  Copyright (c) 2015 Fetchr (http://www.fetchr.us)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class Fetchr_Shipping_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {
        echo 'ok!';
        //
    }

    public function bulkStatusAction() {
        try {
            $shipModel = Mage::getModel('fetchr_shipping/ship_bulkstatus');
            echo json_encode($shipModel->run(true));
        } catch(Exception $e) {
            print $e->getMessage();
        }
    }

    public function testAction() {
        //
        echo 'ok!';
    }
}