<?php
class Biztech_Trackorder_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();     
		$this->getLayout()->getBlock('head')->setTitle($this->__('Track Your Order'));
        $this->renderLayout();
    }
    public function validate(){

    }
    public function initOrder(){
        if ($data = $this->getRequest()->getPost()) {
            $orderId = $data["order_id"];
            $email = $data["email"];
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            $cEmail = $order->getCustomerEmail();
            if($cEmail == trim($email)){
                Mage::register('current_order',$order);    
            } else {
                Mage::register('current_order',Mage::getModel("sales/order"));    
            }
            
        }
    }
    public function trackAction()
    {
        //$orderId = $this->getRequest()->getPost()
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            try {
                if (!Zend_Validate::is(trim($post['order_id']) , 'NotEmpty')) {
                    $error = true;
                }
                if (!Zend_Validate::is(trim($post['email']), 'NotEmpty')) {
                    $error = true;
                }       
                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }   
                if ($error) {
                    throw new Exception();
                }
                $this->initOrder($post);
                $order = Mage::registry('current_order');
                if($order->getId()){
                    
                    $this->getResponse()->setBody($this->getLayout()->getMessagesBlock()->getGroupedHtml().$this->_getGridHtml());
                    return;
                    /*$this->loadLayout();     
                    $this->renderLayout();    */
                } else {
                    Mage::getSingleton('core/session')->addError(Mage::helper('contacts')->__('Order Not Found.Please try again later'));
                    $this->getResponse()->setBody($this->getLayout()->getMessagesBlock()->getGroupedHtml());
                    return;
                }
                

            }catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('trackorder')->__('Please Enter Order Detail.'));
                $this->getResponse()->setBody($this->getLayout()->getMessagesBlock()->getGroupedHtml());
                    return;
                
            }
        } else {
            $this->_redirect('*/*/');
        }

    }
     protected function _getGridHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load("trackorder_index_track");
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
}