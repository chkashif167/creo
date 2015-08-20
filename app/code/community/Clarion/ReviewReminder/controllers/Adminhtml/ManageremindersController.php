<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    6th Jan, 2015
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    Admin manage review reminder controller
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Clarion_ReviewReminder_Adminhtml_ManageremindersController extends Mage_Adminhtml_Controller_Action
{
     /**
     * Pre dispatch action that allows to redirect to no route page in case of 
     * disabled extension through admin panel
     */
    public function preDispatch()
    {
        parent::preDispatch();
        
        if (!Mage::helper('clarion_reviewreminder')->isExtensionEnabled()) {
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect('noRoute');
        }        
    }
    
    /**
     * Init actions
     *
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_title($this->__('Review Reminder'));
        
        $this->loadLayout()
            ->_setActiveMenu('catalog/review_reminder')
            ->_addBreadcrumb(Mage::helper('clarion_reviewreminder')->__('Review Reminder')
                    , Mage::helper('clarion_reviewreminder')->__('Review Reminder'));
        return $this;
    }
    
    /**
     * Index action method
     */
    public function indexAction() 
    {
        $this->_initAction();
        $this->renderLayout();
    }
    
    /**
     * Multiple reminder deletion
     *
     */
    public function massDeleteAction()
    {
        //Get reminder ids from selected checkbox
        $reminderIds = $this->getRequest()->getParam('reminderIds');
        
        if (!is_array($reminderIds)) {
             Mage::getSingleton('adminhtml/session')->addError($this->__('Please select reminder(s).'));
        } else {
            if (!empty($reminderIds)) {
                try {
                    foreach ($reminderIds as $reminderId) {
                        $reminder = Mage::getSingleton('clarion_reviewreminder/reviewreminder')->load($reminderId);
                        //delete record
                        $reminder->delete();
                    }
                     Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($reminderIds))
                    );
                } catch (Exception $e) {
                     Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/');
    }
    
    /**
     * Send multiple reminder
     *
     */
    public function massSendReminderAction()
    {
        //Get reminder ids from selected checkbox
        $reminderIds = $this->getRequest()->getParam('reminderIds');
        
        if (!is_array($reminderIds)) {
             Mage::getSingleton('adminhtml/session')->addError($this->__('Please select reminder(s).'));
        } else {
            if (!empty($reminderIds)) {
                try {
                    $reminderSentCount = 0;
                    foreach ($reminderIds as $reminderId) {
                        $reminder = Mage::getSingleton('clarion_reviewreminder/reviewreminder')->load($reminderId);
                        $customerId = $reminder->getCustomerId();
                        if($customerId){
                            
                            //check config settings
                            /*
                            if(!Mage::Helper('clarion_reviewreminder')->isMatchAllConfigSettings($reminder)){
                                continue;
                            }
                            */
                            
                            //send reminder mail
                            $isMailSent = Mage::Helper('clarion_reviewreminder/mail')->sendReminderEmail($reminder);
                            
                            //update reminder record
                            if($isMailSent){
                                $reminder->setIsReminderSent(1);
                                $reminderCount = $reminder->getReminderCount();
                                //Increment reminder count by 1
                                $reminderCount++;
                                $reminder->setReminderCount($reminderCount);
                                $currentTimestamp = time();
                                $reminder->setSentAt($currentTimestamp);
                                $reminder->setUpdatedAt($currentTimestamp);
                                $reminder->save();
                            }
                        }
                        $reminderSentCount++;
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__('Total of %d reminder(s) have been sent.', $reminderSentCount)
                    );
                } catch (Exception $e) {
                     Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/');
    }
    /**
     * Create new reminder action
     */
    public function newAction()
    {
        $this->_initAction()
           ->_addBreadcrumb(Mage::helper('clarion_reviewreminder')->__('Add Reminders'),
               Mage::helper('clarion_reviewreminder')->__('Add Reminders'));
        $this->_addContent($this->getLayout()->createBlock('clarion_reviewreminder/adminhtml_addreminder'));
        $this->renderLayout();
    }
    
    /**
     * Edit store action
     */
    public function editAction()
    {
        $this->_title($this->__('Review Reminder'))
             ->_title($this->__('Manage Review Reminders'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('reminder_id');
        $model = Mage::getModel('clarion_reviewreminder/reviewreminder');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('clarion_reviewreminder')->__('This reminder no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Store'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getStorelocatorData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('storelocator_data', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('clarion_reviewreminder')->__('Edit Store')
                    : Mage::helper('clarion_reviewreminder')->__('New Store'),
                $id ? Mage::helper('clarion_reviewreminder')->__('Edit Store')
                    : Mage::helper('clarion_reviewreminder')->__('New Store'));
        
         $this->_addContent($this->getLayout()->createBlock('clarion_storelocator/adminhtml_storelocator_edit'))
                 ->_addLeft($this->getLayout()->createBlock('clarion_storelocator/adminhtml_storelocator_edit_tabs'));

        $this->renderLayout();
    }
    
    /**
     * Add multiple reminder
     *
     */
    public function massAddReminderAction()
    {
        //Get item ids from selected checkbox
        $itemIds = $this->getRequest()->getParam('itemIds');
        
        if (!is_array($itemIds)) {
             Mage::getSingleton('adminhtml/session')->addError($this->__('Please select reminder(s).'));
        } else {
            if (!empty($itemIds)) {
                try {
                    $reminderAddCount = 0;
                    $transactionSave = Mage::getModel('core/resource_transaction');
                    foreach ($itemIds as $itemId) {
                        $item = Mage::getModel('sales/order_item')->load($itemId);
                        //check order id
                        $orderId = $item->getOrderId();
                        if(empty($orderId)){
                            continue;
                        }
                
                        //check product id
                        $productId = $item->getProductId();
                        if(empty($productId)){
                            continue;
                        }
                        
                        //check customer id
                        $order = Mage::getModel('sales/order')->load($orderId);
                        $customerId = $order->getCustomerId();
                        if(empty($customerId)){
                            continue;
                        }
                        
                        //Check is reminder exist
                        if(Mage::Helper('clarion_reviewreminder')->isReminderExist($productId, $customerId)){
                            continue;
                        }

                        //Check is review already added by customer
                        if(Mage::Helper('clarion_reviewreminder')->isReviewAlreadyAdded($productId, $customerId)){
                            continue;
                        }

                        //add reminder
                        $reviewreminder = Mage::getModel('clarion_reviewreminder/reviewreminder');

                        $reviewreminder->setOrderId($orderId);
                        $reviewreminder->setCustomerId($customerId);
                        $reviewreminder->setProductId($productId);
                        $currentTimestamp = time();
                        $reviewreminder->setCreatedAt($currentTimestamp);

                        $transactionSave->addObject($reviewreminder);
                        $reminderAddCount++;
                    }
                    $transactionSave->save();
                    
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__('Total of %d reminder(s) have been added.', $reminderAddCount)
                    );
                } catch (Exception $e) {
                     Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/');
    }
}