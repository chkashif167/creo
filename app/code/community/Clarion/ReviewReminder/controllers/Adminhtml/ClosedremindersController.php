<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    6th Jan, 2015
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    Admin manage closed reminder controller
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Clarion_ReviewReminder_Adminhtml_ClosedremindersController extends Mage_Adminhtml_Controller_Action
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
}