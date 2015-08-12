<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_SocialLogin
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


class Plumrocket_SocialLogin_Model_Observer
{

    public function controllerActionPredispatch()
    {
        $helper = Mage::helper('pslogin');
        if(!$helper->moduleEnabled()) {
            return;
        }

        // Check email.
        $request = Mage::app()->getRequest();
        $requestString = $request->getRequestString();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        $editUri = 'customer/account/edit';

        switch(true) {

            case (stripos($requestString, 'customer/account/logout') !== false):
                break;

            case $moduleName = (stripos($module, 'customer') !== false) ? 'customer' : null:
            // case $moduleName = (stripos($module, 'checkout') !== false && stripos($controller, 'onepage') !== false && stripos($action, 'index') !== false) ? 'checkout' : null:

                $session = Mage::getSingleton('customer/session');
                if($session->isLoggedIn() && $helper->isFakeMail()) {
                    
                    $session->getMessages()->deleteMessageByIdentifier('fakeemail');
                    $message = $helper->__('Your account needs to be updated. The email address in your profile is invalid. Please indicate your valid email address by going to the <a href="%s">Account edit page</a>', Mage::getUrl($editUri));

                    switch($moduleName) {
                        case 'customer':
                            if(stripos($requestString, $editUri) !== false) {
                                // Set new message and red field.
                                $message = $helper->__('Your account needs to be updated. The email address in your profile is invalid. Please indicate your valid email address.');
                            }
                            $session->addUniqueMessages(Mage::getSingleton('core/message')->notice($message)->setIdentifier('fakeemail'));
                            break;

                        /*case 'checkout':
                            $session->addUniqueMessages(Mage::getSingleton('core/message')->notice($message)->setIdentifier('fakeemail'));
                            break;*/
                    }
                    
                }
                break;
        }
    }

    public function customerLogin($observer)
    {
        $helper = Mage::helper('pslogin');
        if(!$helper->moduleEnabled()) {
            return;
        }

        // Set redirect url.
        $redirectUrl = $helper->getRedirectUrl('login');
        Mage::getSingleton('customer/session')->setBeforeAuthUrl($redirectUrl);
    }

    public function customerRegisterSuccess($observer)
    {
        $helper = Mage::helper('pslogin');
        if(!$helper->moduleEnabled()) {
            return;
        }

        $data = Mage::getSingleton('customer/session')->getData('pslogin');
        
        if(!empty($data['provider']) && !empty($data['timeout']) && $data['timeout'] > time()) {
            $model = Mage::getSingleton("pslogin/{$data['provider']}");
            
            $customerId = null;
            if($customer = $observer->getCustomer()) {
                $customerId = $customer->getId();
            }

            if($customerId) {
                $model->setUserData($data);

                // Remember customer.
                $model->setCustomerIdByUserId($customerId);

                // Load photo.
                if($helper->photoEnabled()) {
                    $model->setCustomerPhoto($customerId);
                }
            }

        }

        // Show share-popup.
        $helper->showPopup(true);

        // Set redirect url.
        $redirectUrl = $helper->getRedirectUrl('register');
        Mage::app()->getRequest()->setParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_SUCCESS_URL, $redirectUrl);
    }

    public function customerLogout()
    {
        
    }

}