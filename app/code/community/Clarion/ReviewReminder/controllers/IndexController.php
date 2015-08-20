<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    10th Dec, 2014
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    Fontend index controller
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Clarion_ReviewReminder_IndexController extends Mage_Core_Controller_Front_Action
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
    
    function indexAction()
    {
        Mage::Helper('clarion_reviewreminder')->isExtensionEnabled();
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * If customer logined in then redirect page to add review otherwise redirect 
     * page to login and after login redirect to add review.
     *
     */
    
    function addReviewAction()
    {
        $productId  = (int) $this->getRequest()->getParam('product_id');
        $categoryId = (int) $this->getRequest()->getParam('category_id', false);
        
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('review/product/list', array('id' => $productId, 'category' => $categoryId));
        } else {
            $this->_getSession()->setBeforeAuthUrl(Mage::getUrl('review/product/list', 
                    array('id' => $productId, 'category' => $categoryId)));
            $this->_redirect('customer/account/login');
        }
        return;
    }
}