<?php
class Tentura_Ngroups_ManageController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        if ($block = $this->getLayout()->getBlock('customer_newsletter')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->getLayout()->getBlock('head')->setTitle($this->__('Newsletter Subscription'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/account/');
        }
        try {
            
            if (!Mage::helper('ngroups')->isManagementAllowed()){
            
                try {
                    Mage::getSingleton('customer/session')->getCustomer()
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
                    ->save();
                    if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
                        Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been saved.'));
                    } else {
                        Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been removed.'));
                    }
                }
                catch (Exception $e) {
                    Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your subscription.'));
                }
            
            }else{
            
                $groups = $this->getRequest()->getParam('custom_group');

                if (!$groups){
                    
                    Mage::getModel("ngroups/ngroups")->removeSubscriber(Mage::helper("ngroups")->getSubscriberIdByEmail(Mage::getSingleton('customer/session')->getCustomer()->getEmail()), false, false, true);
                    Mage::getSingleton('customer/session')->getCustomer()
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->setIsSubscribed(false)
                    ->save();
                    
                    Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been removed.'));
                
                }else{

                    $subscriberdGroups = Mage::getModel('ngroups/ngroups')->getSubscriberGroups(Mage::helper("ngroups")->getSubscriberIdByEmail(Mage::getSingleton('customer/session')->getCustomer()->getEmail()));

                    Mage::getSingleton('customer/session')->getCustomer()
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->setIsSubscribed(true)
                        ->save();
                    
                    foreach ($groups as $group){
                        if (in_array($group, $subscriberdGroups)){
                            unset($subscriberdGroups[array_search($group, $subscriberdGroups)]);
                        }
                        Mage::getModel("ngroups/ngroups")->addSubscriber(Mage::helper("ngroups")->getSubscriberIdByEmail(Mage::getSingleton('customer/session')->getCustomer()->getEmail()), $group);
                    }
                    foreach ($subscriberdGroups as $subscriberdGroup){
                        Mage::getModel("ngroups/ngroups")->removeSubscriber(Mage::helper("ngroups")->getSubscriberIdByEmail(Mage::getSingleton('customer/session')->getCustomer()->getEmail()), $subscriberdGroup);
                    }
                    
                    Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been saved.'));
         
                }
                
                
            }
        }
        catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your subscription.'));
        }
        $this->_redirect('customer/account/');
    }
}
