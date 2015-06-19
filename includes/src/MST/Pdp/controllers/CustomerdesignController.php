<?php
class MST_Pdp_CustomerdesignController extends Mage_Core_Controller_Front_Action
{
	/*public function preDispatch()
    {
       	parent::preDispatch();
       	if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
			
	    // adding message in customer login page
	    Mage::getSingleton('core/session')
                ->addError(Mage::helper('pdp')->__('Please sign in or create a new account!'));
        }
    }*/
	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	public function deleteAction() {
		$designId = $this->getRequest()->getParam("id");
		if($designId) {
			//Try to delete here
			Mage::getModel("pdp/customerdesign")->load($designId)->delete();
		}
		$this->_redirect("*/*/index");
	}
    public function saveAction() {
        $params = $this->getRequest()->getParams();
        $customer = Mage::getSingleton("customer/session")->getCustomer();
        $params['customer_id'] = $customer->getId();
        $params['created_time'] = now();
        $model = Mage::getModel("pdp/customerdesign")->saveTemplate($params);
        $response = array();
        if($model->getId()) {
            $response['status'] = "success";
            $response['message'] = "Your design has been successfully saved!";
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('pdp')->__($response['message']));
            $this->getResponse()->setBody(json_encode($response));
            //Redirect to My Design page maybe
            $this->_redirect("*/*/index");
            
        }
    }
    public function saveToCustomerLoggedInAction() {
        $postData = $this->getRequest()->getPost();
        $params = $postData['design_info'];
        $response = array(
            'status' => 'error',
            'message' => 'Unable to save customer design!'
        );
        $customer = Mage::getSingleton("customer/session")->getCustomer();
        if($customer->getId()) {
            $params['customer_id'] = $customer->getId();
            $params['created_time'] = now();
            $model = Mage::getModel("pdp/customerdesign")->saveTemplate($params);
            $response = array();
            if($model->getId()) {
                $response['status'] = "success";
                $response['message'] = "Your design has been successfully saved!";
            }
        } else {
            $response = array(
                'status' => 'guest',
                'message' => 'Please login to save the design!'
            );
        }
        $this->getResponse()->setBody(json_encode($response));
    }
}

   	