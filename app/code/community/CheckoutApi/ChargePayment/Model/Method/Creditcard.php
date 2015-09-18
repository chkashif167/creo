<?php
class CheckoutApi_ChargePayment_Model_Method_Creditcard extends CheckoutApi_ChargePayment_Model_Method_Abstract
{
    /**
    * Is this payment method a gateway (online auth/charge) ?
    */
    protected $_isGateway = true;
    protected $_canUseInternal = true;
    protected $_code = 'creditcard';

    protected $_formBlockType = 'checkoutapi_chargePayment/form_creditcard';
   // protected $_infoBlockType = 'checkoutapi_chargePayment/info_creditcard';

    /**
     * @param Varien_Object $payment
     * @param $amount
     * @param array $extraConfig
     * @return mixed
     */


    protected function _createCharge(Varien_Object $payment,$amount,$extraConfig = array())
    {
        /** @var CheckoutApi_Client_ClientGW3  $Api */
        $Api = CheckoutApi_Api::getApi(array('mode'=>$this->getConfigData('mode')));
        $config = parent::_createCharge($payment,$amount,$extraConfig);
        $config['paymentToken'] = $payment->getAdditionalInformation('cko_cc_paymenToken');
        $config['authorization'] = $this->getConfigData('privatekey');
        return $Api->verifyChargePaymentToken($config);

    }

    protected  function _isRedirect($payment)
    {
        $cko_cc_redirectUrl = $payment->getAdditionalInformation('cko_cc_redirectUrl');


        if($cko_cc_redirectUrl) {
            return true;
        }
        return false;
    }

    protected function _capture ( Varien_Object $payment , $amount )
    {
        $extraConfig = array (
            'autoCapture' => CheckoutApi_Client_Constant::AUTOCAPUTURE_CAPTURE ,
            'autoCapTime' => $this->getConfigData ( 'auto_capture_time' )
        );
        $this->setPendingState($payment);
        if(!$this->_isRedirect($payment)) {
            $this->_placeOrder($payment, $amount, "Payment has been successfully captured for Transaction ", $extraConfig);
        }
    }

/**
 * Authorize payment abstract method
 *
 * @param Varien_Object $payment
 * @param float $amount
 *
 * @return CheckoutApi_ChargePayment_Model_Method_Creditcard
 */

    public function authorize ( Varien_Object $payment , $amount )
    {
        if ( !$this->canAuthorize () ) {
            Mage::throwException ( Mage::helper ( 'payment' )->__ ( 'Authorize action is not available.' ) );
        } else {
            $extraConfig = array (
                'autoCapture' => CheckoutApi_Client_Constant::AUTOCAPUTURE_AUTH ,
                'autoCapTime' => 0
            );
            $this->setPendingState($payment);
            if(!$this->_isRedirect($payment)) {
                $this->_placeOrder ( $payment , $amount , "Payment has been successfully authorize for Transaction " , $extraConfig );
            }
        }

        return $this;
    }

    public function validate()
    {
        /**
         * to validate payment method is allowed for billing country or not
         */
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $billingCountry = $paymentInfo->getOrder()->getBillingAddress()->getCountryId();
        } else {
            $billingCountry = $paymentInfo->getQuote()->getBillingAddress()->getCountryId();
        }
        if (!$this->canUseForCountry($billingCountry)) {
            Mage::throwException(Mage::helper('payment')->__('Selected payment type is not allowed for billing country.'));
        }
        return $this;
    }

    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        parent::assignData($data);
        $info = $this->getInfoInstance();
        $details['cko_cc_paymenToken'] = $data->getData('cko_cc_paymenToken');
        $details['cko_cc_redirectUrl'] = $data->getData('cko_cc_redirectUrl');
        $info->setAdditionalInformation('cko_cc_paymenToken',$details['cko_cc_paymenToken']);
        $info->setAdditionalInformation('cko_cc_redirectUrl',$details['cko_cc_redirectUrl']);
        $info->setAdditionalData(serialize($details));
        $info->setPaymentToken( $details['cko_cc_paymenToken']);
        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
        $info = $this->getInfoInstance();
        $toReturn = null;
        $dataOrder= $this->getCentinelValidationData();
        $block = Mage::getBlockSingleton('checkoutapi_chargePayment/form_creditcard')->getPaymentTokenResult($dataOrder->getOrderNumber());
        $paymentToken = $block['token'];
        
        $cko_cc_redirectUrl = $info->getAdditionalInformation('cko_cc_redirectUrl');
        
        if($cko_cc_redirectUrl){
            $cko_cc_redirectUrl = Mage::helper('checkoutapi_chargePayment')->replace_between($cko_cc_redirectUrl, 'paymentToken=', '&', $paymentToken);
            $toReturn = $cko_cc_redirectUrl.'&trackId='.$dataOrder->getOrderNumber();
        }
        return $toReturn;
    }

}