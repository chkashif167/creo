<?php
class CheckoutApi_ChargePayment_Model_Method_LocalPayment extends CheckoutApi_ChargePayment_Model_Method_Abstract
{
    protected $_code = 'localpayment';
    /**
     * Is this payment method a gateway (online auth/charge) ?
     */
    protected $_isGateway = true;
    protected $_canUseInternal = true;


    protected $_formBlockType = 'checkoutapi_chargePayment/form_localpaymnet';
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
        $scretKey = $this->getConfigData('privatekey');
        $order = $payment->getOrder();
        $billingaddress = $order->getBillingAddress();
        $currencyDesc = $order->getBaseCurrencyCode();
        $orderId = $order->getIncrementId();
        $amountCents = $amount*100;
        $config = array();
        $config['authorization'] = $scretKey  ;
        $config['mode'] = $this->getConfigData('mode');
        $config['timeout'] = $this->getConfigData('timeout');
        $config['postedParam'] = array ( 'email'=>$billingaddress->getData('email'),
            'amount'=>$amountCents,
            'currency'=> $currencyDesc,
            'description'=>"Order number::$orderId",
        );

        $config['postedParam'] = array_merge($config['postedParam'],$extraConfig);
        $config['postedParam']['token'] = $payment->getCkoCcToken();

        return $Api->createCharge($config);

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


}