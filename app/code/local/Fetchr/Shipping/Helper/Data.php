<?php
/**
 * Fetchr
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
 * versions in the future. If you wish to customize Fetchr Magento Extension (Fetchr Shiphappy) for your
 * needs please refer to http://www.fetchr.us for more information.
 *
 * @author     Danish Kamal
 * @package    Fetchr Shiphappy
 * @copyright  Copyright (c) 2015 Fetchr (http://www.fetchr.us)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Fetchr_Shipping_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $accountType;
  	protected $serviceType;
	protected $userName;
    protected $password;
    protected $userId;
    protected $connections = array();
        public function init() {
        $this->accountType = Mage::getStoreConfig('carriers/fetchr/accounttype');
        $this->serviceType = Mage::getStoreConfig('carriers/fetchr/servicetype');
        $this->userName = Mage::getStoreConfig('carriers/fetchr/username');
        $this->password = Mage::getStoreConfig('carriers/fetchr/password');
    }
}