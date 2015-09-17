<?php

class DigitalPianism_AjaxLogin_IndexController extends Mage_Core_Controller_Front_Action
{

    public function forgotpasswordAction()
    {
        $session = Mage::getSingleton('customer/session');

        if ($session->isLoggedIn()) {
            return;
        }

        $email = $this->getRequest()->getPost('email');
        $result = array(
            'success' => false
        );
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $session->setForgottenEmail($email);
                $result['error'] = Mage::helper('checkout')->__('Invalid email address.');
            } else {
                $customer = Mage::getModel('customer/customer')
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->loadByEmail($email);

                if ($customer->getId()) {
                    try {
                        $customerHelper = Mage::helper('customer');
                        if (method_exists($customerHelper, 'generateResetPasswordLinkToken')) {
                            $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                            $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                            $customer->sendPasswordResetConfirmationEmail();
                        } else {
                            // 1.6.0.x and earlier
                            $newPassword = $customer->generatePassword();
                            $customer->changePassword($newPassword, false);
                            $customer->sendPasswordReminderEmail();
                            $result['message'] = Mage::helper('customer')->__('A new password has been sent.');
                        }
                        $result['success'] = true;
                    } catch (Exception $e) {
                        $result['error'] = $e->getMessage();
                    }
                }
                if (!isset($result['message']) && ($result['success'] || !$customer->getId())) {
                    $result['message'] = Mage::helper('customer')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('customer')->escapeHtml($email));
                }
            }
        } else {
            $result['error'] = Mage::helper('customer')->__('Please enter your email.');
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function loginAction()
    {
        $session = Mage::getSingleton('customer/session');

        if ($session->isLoggedIn()) {
            return;
        }

        $result = array(
            'success' => false
        );

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    $result['redirect'] = $this->_getRefererUrl() ? $this->_getRefererUrl() : Mage::getUrl('customer/account', array('_secure' => true));
                    $result['success'] = true;
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', Mage::helper('customer')->getEmailConfirmationUrl($login['username']));
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $result['error'] = $message;
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    Mage::helper("ajaxlogin")->log("There has been an error during the login.");
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $result['error'] = Mage::helper('customer')->__('Login and password are required.');
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function logoutAction()
    {
        $session = Mage::getSingleton('customer/session');

        if (!$session->isLoggedIn()) {
            return;
        }

        $session->logout()->renewSession();
        $result['redirect'] = Mage::getUrl('customer/account/logoutSuccess', array('_secure' => true));
        $result['success'] = true;

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}