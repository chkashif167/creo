<?php
class Magestore_Fblogin_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		$isAuth = $this->getRequest()->getParam('auth');
		$facebook = Mage::helper('fblogin')->createFacebook();
		$userId = $facebook->getUser();
		
		if($isAuth && !$userId && $this->getRequest()->getParam('error_reason') == 'user_denied'){
			echo("<script>window.close()</script>");
		}elseif ($isAuth && !$userId){
			$loginUrl = $facebook->getLoginUrl(array('scope' => 'email'));
			echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
			exit;
		}

		$user = Mage::helper('fblogin')->getFbUser();
		if ($isAuth && $user){
			$nextUrl = Mage::helper('fblogin')->getFbloginUrl();
			die("<script type=\"text/javascript\">window.opener.location.href=\"".$nextUrl."\"; window.close();</script>");
    	}
	
		$store_id = Mage::app()->getStore()->getStoreId(); //add them
		$website_id = Mage::app()->getStore()->getWebsiteId();//add them
		$data =  array('firstname'=>$user['first_name'], 'lastname'=>$user['last_name'], 'email'=>$user['email']);
		$customer = $this->getCustomerByEmail($data['email'], $website_id); //edit

		if(!$customer || !$customer->getId()){
			// $customer = $this->createCustomer($data);	
			$customer = $this->createCustomerMultiWebsite($data, $website_id, $store_id);	//add them		
		}
		//add old
		if ($customer->getConfirmation())
		{
			  try {
				  $customer->setConfirmation(null);
				  $customer->save();
			  }catch (Exception $e) {
				  Mage::getSingleton('core/session')->addError(Mage::helper('fblogin')->__('Error'));
			  }

		}
		
		Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
		$this->_loginPostRedirect();
		//$this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());//add them fix new
    }
	
	protected function getCustomerByEmail($email, $website_id){//add them
		$collection = Mage::getModel('customer/customer')->getCollection()
					->addFieldToFilter('email', $email);
					// ->addFieldToFilter('website_id', $website_id) //add them
					// ->getFirstItem();
		if (Mage::getStoreConfig('customer/account_share/scope')) {
			$collection->addFieldToFilter('website_id',$website_id);
		}
		return $collection->getFirstItem();
	}
	
	protected function createCustomer($data){
		$customer = Mage::getModel('customer/customer')
					->setFirstname($data['firstname'])
					->setLastname($data['lastname'])
					->setEmail($data['email']);
					
		$isSendPassToCustomer = Mage::getStoreConfig('fblogin/general/is_send_password_to_customer');
		$newPassword = $customer->generatePassword();
		$customer->setPassword($newPassword);
		try{
			$customer->save();
		}catch(Exception $e){}
		
		if($isSendPassToCustomer)
			$customer->sendPasswordReminderEmail();
		return $customer;
	}
	// add them 
	protected function createCustomerMultiWebsite($data, $website_id, $store_id)
	{
		$customer = Mage::getModel('customer/customer')->setId(null);
		$customer ->setFirstname($data['firstname'])
					->setLastname($data['lastname'])
					->setEmail($data['email'])
					->setWebsiteId($website_id)
					->setStoreId($store_id)
					->save()
					;
		$isSendPassToCustomer = Mage::getStoreConfig('fblogin/general/is_send_password_to_customer');
		$newPassword = $customer->generatePassword();
		$customer->setPassword($newPassword);
		try{
			$customer->save();
		}catch(Exception $e){}
		
		if($isSendPassToCustomer)
			$customer->sendPasswordReminderEmail();
		return $customer;
	}
	//add old
		protected function _loginPostRedirect()
    {
        $session = Mage::getSingleton('customer/session');

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
            
        } else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
        $this->_redirectUrl($session->getBeforeAuthUrl(true));
	}
	//
}