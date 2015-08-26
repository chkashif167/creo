<?php
class CheckoutApi_ChargePayment_Model_Method_Creditcardpci extends CheckoutApi_ChargePayment_Model_Method_Abstract
{
    /**
    * Is this payment method a gateway (online auth/charge) ?
    */
    protected $_isGateway = true;
    protected $_canUseInternal = true;
    protected $_code = 'creditcardpci';

    protected $_formBlockType = 'checkoutapi_chargePayment/form_creditcardpci';
   // protected $_infoBlockType = 'checkoutapi_chargePayment/info_creditcard';
    /**
     * @param Varien_Object $payment
     * @param $amount
     * @param array $extraConfig
     * @return mixed
     */

    protected  function _createCharge(Varien_Object $payment,$amount,$extraConfig = array())
    {

        /** @var CheckoutApi_Client_ClientGW3  $Api */
        $Api = CheckoutApi_Api::getApi(array('mode'=>$this->getConfigData('mode')));

        $order = $payment->getOrder();
        $billingAddress = $order->getBillingAddress();
        $config = parent::_createCharge($payment,$amount,$extraConfig);
        $config['postedParam']['email'] = $billingAddress->getData('email');
        $config['postedParam']['card'] =  array_merge (
                                                        array(
                                                                'phoneNumber'   =>    $billingAddress->getData('telephone'),
                                                                'name'          =>    $payment->getCcOwner(),
                                                                'number'        =>    $payment->getCcNumber(),
                                                                'expiryMonth'   =>    (int) $payment->getCcExpMonth(),
                                                                'expiryYear'    =>    (int)$payment->getCcExpYear(),
                                                                'cvv'           =>    $payment->getCcCid(),
                                                             ),
                                                        $config['postedParam']['card']
                                                );


        return $Api->createCharge($config);

    }

    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        parent::assignData($data);
        $info = $this->getInfoInstance();
        $info->setCcOwner($data->getCcOwner());

        return $this;
    }


}