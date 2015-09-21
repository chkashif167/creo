<?php
class CheckoutApi_ChargePayment_IndexController extends Mage_Core_Controller_Front_Action
{
	private $_code    =    null;

	private function _process()
	{
		$config['chargeId']    =    $this->getRequest()->getParam('chargeId');
		$config['authorization']    =    $this->_requesttConfigData('privatekey');
		$Api    =    CheckoutApi_Api::getApi(array('mode'=>$this->_requesttConfigData('mode')));
		$respondBody =    $Api->getCharge($config);
		$json = $respondBody->getRawOutput();
		return $json;
	}


	public function processAction()
	{
		if($this->getRequest()->getParam('chargeId')) {
			$stringCharge = $this->_process();
		}else {
			$stringCharge = file_get_contents("php://input");
		}



		if($stringCharge) {

			$Api    =    CheckoutApi_Api::getApi(array('mode'=>$this->_requesttConfigData('mode')));
			$objectCharge = $Api->chargeToObj($stringCharge);

			if($chargeId = $objectCharge->getId()) {
				/** @var Mage_Sales_Model_Resource_Order_Payment_Transaction  $transactionObject */

				$_order = Mage::getModel('sales/order')->loadByIncrementId($objectCharge->getTrackId());
				$transactionObject = Mage::getModel('sales/order_payment_transaction')
					->load($_order->getId(),'order_id');

				if($orderId = $transactionObject->getOrderId()) {

					$_order = Mage::getModel('sales/order')->load($orderId);
					$_payment = $_order->getPayment();
					$chargeIdPayment = preg_replace('/\-capture$/','',$_payment->getLastTransId());
					$chargeIdPayment = preg_replace('/\-void$/','',$chargeIdPayment);

					$this->setCode($_payment->getMethod());

					//if($chargeIdPayment == $chargeId) {

						if($objectCharge->getCaptured() && ($_order->getStatus()!=
							'canceled' || $_order->getStatus()!=
								'complete' )) {
							$transactionCapture = Mage::getModel('sales/order_payment_transaction')
								->load($chargeId.'-'.Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,'txn_id');


							if(!$transactionCapture->getOrderId()) {
								/** @var Mage_Sales_Model_Order_Payment $_payment  */

								$_payment->setParentTransactionId($chargeId);
								$_payment->capture ( null );
								$orderStatus = $this->_requesttConfigData ( 'order_status_capture' );
								$_rawInfo = $objectCharge->toArray ();

								$_payment->setAdditionalInformation ( 'rawrespond' , $_rawInfo )
										->setShouldCloseParentTransaction('Completed' === $orderStatus)
										->setIsTransactionClosed(0)
											->setTransactionAdditionalInfo ( Mage_Sales_Model_Order_Payment_Transaction
											::RAW_DETAILS , $_rawInfo );
									$_payment->save();

								$_order->setStatus ( $orderStatus , false );
								$_order->addStatusToHistory ( $orderStatus , 'Payment successfully  captured
                                with Transaction ID ' . $objectCharge->getId () );
								$_order->save ();
								$this->getResponse()->setBody('Payment successfully  captured
                                with Transaction ID '.$objectCharge->getId());

							}else {

								$this->getResponse()->setBody('Payment was already captured
                                with Transaction ID '.$objectCharge->getId());
							}

						} elseif($objectCharge->getRefunded() ) {
//cancel order
						}elseif($objectCharge->getVoided() || $objectCharge->getExpired()) {

							$transactionVoid = Mage::getModel('sales/order_payment_transaction')
								->load($chargeId.'-'.Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID,'txn_id');
							if(!$transactionVoid->getOrderId()) {
								$_voidObj = new Varien_Object();
								$_order->getPayment ()
									->setTransactionId ( null )
									->setParentTransactionId ( $objectCharge->getId () )
									->void ( new Varien_Object() );
								$_order->registerCancellation ( 'Transaction has been void' )
									->save ();
								$_rawInfo = $objectCharge->toArray ();
								$_payment->setAdditionalInformation ( 'rawrespond' , $_rawInfo );
								$_payment->setTransactionAdditionalInfo (
									Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS , $_rawInfo );
								$_payment->setTransactionId ( $objectCharge->getId () );

								$_payment
									->setIsTransactionClosed ( 1 )
									->setShouldCloseParentTransaction ( 1 );

								$_payment->addTransaction ( Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID ,
									null , false , 'Transaction has been void' );
								$_payment->void ($_voidObj);
								$_payment->unsLastTransId ();
								$this->getResponse()->setBody('Payment void
                                with Transaction ID '.$objectCharge->getId());
							}else {

								$this->getResponse()->setBody('Payment already void
                                with Transaction ID '.$objectCharge->getId());
							}
						}


					//}

				}


			}



		} else {
			Mage::throwException ( Mage::helper ( 'payment' )->__ ( 'Fail: No Charge object posted' ));

		}
	}

	public function getCode()
	{
		if(!$this->_code) {
			$this->setCode();
		}
		return $this->_code;
	}

	private function _requesttConfigData($field, $storeId = null)
	{
		if (null === $storeId) {
			$storeId = Mage::app()->getStore()->getId();
		}

		$path = 'payment/'.$this->getCode().'/'.$field;
		return Mage::getStoreConfig($path, $storeId);
	}

	private function setCode($code = '')
	{
		if(!$code) {
			$storeId = Mage::app ()->getStore ()->getId ();
			if ( Mage::getStoreConfig ( 'payment/creditcard/active' , $storeId ) ) {
				$this->_code = 'creditcard';

			} elseif ( Mage::getStoreConfig ( 'payment/creditcardpci/active' , $storeId ) ) {
				$this->_code = 'creditcardpci';
			}
		}else {

			$this->_code = $code;
		}
	}

    public  function callbackAction()
    {
        $postedVal = $this->getRequest()->getParams();
        if(!empty($postedVal) && isset($postedVal['cko-payment-token']) && isset($postedVal['cko-track-id'])) {
            $order_id  = $postedVal['cko-track-id'];
            $paymentToken  = $postedVal['cko-payment-token'];
            $storeId = Mage::app ()->getStore ()->getId ();

            $Api = CheckoutApi_Api::getApi(array('mode'=>$this->_requesttConfigData('mode')));
            $config['paymentToken'] = $paymentToken;
            $config['authorization'] = $this->_requesttConfigData('privatekey');
            $chargeObject = $Api->verifyChargePaymentToken($config);
            $_order = Mage::getModel('sales/order')->loadByIncrementId($order_id);

            $_payment = $_order->getPayment();
            $chargeUpdated = $Api->updateTrackId($chargeObject, $order_id);
            if($chargeObject->isValid()) {
                $chargeId = $chargeObject->getId();
                if ($chargeObject->getStatus() == 'Authorised' || $chargeObject->getStatus() == 'Flagged') {
                    $_payment->setTransactionId($chargeId);
                    $_payment->setParentTransactionId($chargeId);
                    $_payment->authorize ( true,$_order->getBaseTotalDue() );
                    $orderStatus = $this->_requesttConfigData ( 'order_status' );
                    $_rawInfo = $chargeObject->toArray ();
                    $_payment->setAdditionalInformation ( 'rawrespond' , $_rawInfo )
                        ->setShouldCloseParentTransaction('Completed' === $orderStatus)
                        ->setIsTransactionClosed(0)
                        ->setTransactionAdditionalInfo ( Mage_Sales_Model_Order_Payment_Transaction
                        ::RAW_DETAILS , $_rawInfo );
                    $_payment->save();
                    $_order->setStatus ( $orderStatus , false );
                    $_order->addStatusToHistory ( $orderStatus , 'Payment successfully '.$chargeObject->getStatus().'
                    with Transaction ID ' . $chargeObject->getId () );
                    $_order->save ();
                    $this->getResponse()->setBody('Payment successfully '.$chargeObject->getStatus().'
                                with Transaction ID '.$chargeObject->getId());
                    $this->_redirect('checkout/onepage/success', array('_secure'=>true));

                }elseif($chargeObject->getStatus() == 'Voided'  ) {

                }elseif($chargeObject->getStatus() == 'Decline') {

                }
            }// end is valid
        }//end if posted empty

    }

}