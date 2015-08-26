<?php
class CheckoutApi_ChargePayment_Adminhtml_ChargeProcessController extends Mage_Adminhtml_Controller_Action
{

    protected $_code = 'creditcard';

    public function CaptureAction()
    {
        $_id = $this->getRequest()->getParam('order_id');
        /** @var Mage_Sales_Model_Order $_order */
        $_order = Mage::getModel('sales/order')->load($_id);
        $_payment = $_order->getPayment();
        $chargeId = preg_replace('/\-capture$/','',$_payment->getLastTransId());
        $_method = $_payment->getMethod();

        if($_method) {
            $this->_code = $_method;
        }

        $secretKey = $this->getConfigData('privatekey');
        $_config = array();
        $_config['authorization'] = $secretKey ;
        $_config['chargeId'] = $chargeId ;
        $_chargeObj = $this->_getCharge($_config);
        $hasBeenCaptured = false;
        $_captureObj = $_chargeObj;

        if($_chargeObj) {

            if(!$_chargeObj->getCaptured()){
                $_authorizeAmount = $_payment->getAmountAuthorized();
                /** @var CheckoutApi_Client_ClientGW3  $Api */
                $_Api = CheckoutApi_Api::getApi(array('mode'=>$this->getConfigData('mode')));
                $_config['postedParam'] = array (
                                                'value'=>(int)($_authorizeAmount*100)
                                                );

                $_captureCharge = $_Api->captureCharge($_config);
                if($_captureCharge->isValid() && $_captureCharge->getStatus() == 'Captured' &&
                    preg_match('/^1[0-9]+$/',$_captureCharge->getResponseCode()) ) {
                    $hasBeenCaptured = true;
                    $_captureObj = $_captureCharge;
                }

            } else {

                $hasBeenCaptured = true;
                $_captureObj = $_chargeObj;

            }//!$_chargeObj->getCaptured()
        }


        if($hasBeenCaptured) {

            if ($_payment->getMethodInstance() instanceof CheckoutApi_ChargePayment_Model_Method_Abstract) {
                $transactionCapture = Mage::getModel('sales/order_payment_transaction')
                    ->load($chargeId.'-'.Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,'txn_id');


                if(!$transactionCapture->getOrderId()) {
                    $orderStatus = $this->getConfigData ( 'order_status_capture' );
                    $_payment->setParentTransactionId($chargeId);
                    $_payment->capture ( null );
                    $_rawInfo = $_captureObj->toArray ();
                    $_payment->setAdditionalInformation ( 'rawrespond' , $_rawInfo )
                             ->setShouldCloseParentTransaction('Completed' === $orderStatus)
                             ->setIsTransactionClosed(0)
                             ->setTransactionAdditionalInfo ( Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS
                                 , $_rawInfo );
                    $_payment->save();

                    $_order->setStatus ( $orderStatus , false );

                    $_order->addStatusToHistory ( $orderStatus , 'Payment Sucessfully captured
                  with Transaction ID ' . $_captureObj->getId () );

                    $_order->save ();
                    Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( 'Payment Sucessfully Placed
                  with Transaction ID ' . $_captureObj->getId () );
                }else {
                    Mage::getSingleton ( 'adminhtml/session' )->addWarning ( 'Payment was already captured
                  with Transaction ID ' . $_captureObj->getId () );
                }
            }


        } else {
            if($_captureObj){
                Mage::getSingleton('adminhtml/session')->addError($_captureObj->getExceptionState()->getErrorMessage());

            }else {
                Mage::getSingleton('adminhtml/session')->addError('An unexpected error has occured. Please contact
                checkout.com support team');
            }

        }


     $this->_redirectReferer();
    }

    private  function _getCharge($_config)
    {
        /** @var CheckoutApi_Client_ClientGW3  $Api */
        $_Api       =   CheckoutApi_Api::getApi(array('mode'=>$this->getConfigData('mode')));
        $_return    =   false;
        $charge     =   $_Api->getCharge($_config);

        if($charge->isValid()){
            $_return    =   $charge;
        }

        return  $_return;
    }

    public  function  VoidAction()
    {
        $_id = $this->getRequest()->getParam('order_id');
        $_order = Mage::getModel('sales/order')->load($_id);

        $_payment = $_order->getPayment();
        $chargeId = preg_replace('/\-capture$/','',$_payment->getLastTransId());
        $_authorizeAmount = $_payment->getAmountAuthorized();
        /** @var CheckoutApi_Client_ClientGW3  $Api */

        $_Api = CheckoutApi_Api::getApi(array('mode'=>$this->getConfigData('mode')));
        $secretKey = $this->getConfigData('privatekey');
        $_config = array();
        $_config['authorization'] = $secretKey ;
        $_config['chargeId'] = $chargeId ;
        $_config['postedParam'] = array (
            'value'=>(int)($_authorizeAmount*100)
        );

        $_refundCharge = $_Api->voidCharge($_config);
        $_payment->void(
            new Varien_Object()
        );

        if($_refundCharge->isValid() && $_refundCharge->getRefunded() &&
            preg_match('/^1[0-9]+$/',$_refundCharge->getResponseCode())) {
            if ($_payment->getMethodInstance() instanceof CheckoutApi_ChargePayment_Model_Method_Creditcard) {
                $_voidObj =  new Varien_Object();
                $_id = $this->getRequest()->getParam('order_id');
                /** @var Mage_Sales_Model_Order $_order */
                $_order = Mage::getModel('sales/order')->load($_id);

                $_order->getPayment()
                    ->setTransactionId(null)
                    ->setParentTransactionId($_refundCharge->getId())
                    ->void( new Varien_Object());
            }
            $_order->registerCancellation('Transaction has been void')
                ->save();

                $_rawInfo = $_refundCharge->toArray();
                $_payment->setAdditionalInformation('rawrespond',$_rawInfo);
                $_payment->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,$_rawInfo);
                $_payment->setTransactionId($_refundCharge->getId());

                $_payment
                    ->setIsTransactionClosed(1)
                    ->setShouldCloseParentTransaction(1);

                $_payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID, null, false , 'Transaction has been void');
                $_payment->void(
                    $_voidObj
                );
                $_payment->unsLastTransId();


        }else {

            Mage::getSingleton('adminhtml/session')->addError($_refundCharge->getExceptionState()->getErrorMessage());

        }

        $this->_redirectReferer();
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|string|null|Mage_Core_Model_Store $storeId
     *
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = Mage::app()->getStore()->getStoreId();
        }
        $path = 'payment/'.$this->getCode().'/'.$field;
        return Mage::getStoreConfig($path, $storeId);
    }


    public  function getCode()
    {
        return $this->_code;
    }
} 