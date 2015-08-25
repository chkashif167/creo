<?php

class Evopin_Checkoutcom_Model_Payment extends Mage_Payment_Model_Method_Cc
{
    protected $_code  = 'checkoutcom';

	const PAYMENTMODULE_API_URL_UAT = 'https://api.checkout.com/process/gateway.aspx';
	const PAYMENTMODULE_API_URL_LIVE = 'https://api.checkout.com/process/gateway.aspx';
	const PAYMENTMODULE_TRANS_TYPE_SALE = '1';


    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc				= false;
	protected $_isInitializeNeeded      = false;

	protected $_allowCurrencyCode = array('AUD', 'CAD', 'CHF', 'DKK', 'EUR', 'GBP', 'HKD', 'JPY', 'NOK', 'NZD', 'SEK', 'USD', 'ZAR');

	protected $_formBlockType = 'checkoutcom/form_paymentform';

	private $errorDetails;

	/**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
    // public function canUseForCurrency($currencyCode)
    // {
    //     if (!in_array($currencyCode, $this->_allowCurrencyCode)) {
    //         return false;
    //     }
    //     return true;
    // }

	private function getApiUrl()
    {
        if (!$this->getConfigData('gateway_url')) {
            return self::PAYMENTMODULE_API_URL;
        }
        return $this->getConfigData('gateway_url');
    }
	private function getHeaders()
    {
        return array("MIME-Version: 1.0", "Content-type: application/x-www-form-urlencoded", "Contenttransfer-encoding: text");
    }
	private function getNVPArray(Varien_Object $payment, $method,$action)
    {
		$order = $payment->getOrder();
		$billing = $payment->getOrder()->getBillingAddress();
		$shipping = $order->getShippingAddress();



			   $post_XML= '<request>' .
					'<merchantid>' . Mage::helper('core')->decrypt($this->getConfigData('user_name')) . '</merchantid>' .
					'<password>' . Mage::helper('core')->decrypt($this->getConfigData('password')) . '</password>' .
					'<action>'. $action . '</action>' .
					'<trackid>' . $order->getIncrementId() . '</trackid>' .
					'<bill_currencycode>' .  $order->getBaseCurrencyCode() . '</bill_currencycode>' .
					'<bill_cardholder>' . $payment->getCcOwner() . '</bill_cardholder>' .
					'<bill_cc_type>' . CC . '</bill_cc_type>' .
					'<bill_cc_brand></bill_cc_brand>' .
					'<bill_cc>' . $payment->getCcNumber() . '</bill_cc>' .
					'<bill_expmonth>' . $payment->getCcExpMonth() . '</bill_expmonth>' .
					'<bill_expyear>' . $payment->getCcExpYear() . '</bill_expyear>' .
					'<bill_cvv2>' . $payment->getCcCid() . '</bill_cvv2>' .
					'<bill_address>' . $billing->getStreet(1) . '</bill_address>' .
					'<bill_address2></bill_address2>' .
					'<bill_postal>' . $billing->getPostcode() . '</bill_postal>' .
					'<bill_city>' . $billing->getCity() . '</bill_city>' .
					'<bill_state>' . $billing->getRegion() . '</bill_state>' .
					'<bill_email>' . $order->getCustomerEmail() . '</bill_email>' .
					'<bill_country>' . $billing->getCountry() . '</bill_country>' .
					'<bill_amount>' . $payment->getAmount() . '</bill_amount>' .
					'<bill_phone>' . $billing->getTelephone() . '</bill_phone>' .
					'<bill_fax>' . '' . '</bill_fax>' .
					'<bill_customerip>' . $billing->getCustomerId() . '</bill_customerip>' .
					'<bill_merchantip>' . $order->getRemoteIp() . '</bill_merchantip>';
					if (!empty($shipping))
					{
					 $post_XML =  $post_XML .'<ship_address>' .  $shipping->getStreet(1) . '</ship_address>' .
								'<ship_email>' . '' . '</ship_email>' .
								'<ship_postal>' .  $shipping->getPostcode() . '</ship_postal>' .
								'<ship_address2></ship_address2>' .
								'<ship_type>' . '' . '</ship_type>' .
								'<ship_city>' . $shipping->getCity() . '</ship_city>' .
								'<ship_state>' . $shipping->getRegion() . '</ship_state>' .
								'<ship_phone>' . '' . '</ship_phone>' .
								'<ship_country>' . $shipping->getCountry() . '</ship_country>' .
								'<ship_fax>' . '' . '</ship_fax>' ;
					}
					$post_XML =  $post_XML . '<udf1></udf1>' .
								'<udf2></udf2>' .
								'<udf3></udf3>' .
								'<udf4></udf4>' .
								'<udf5></udf5>' .
								'</request>';


		return  $post_XML;

    }
	private function getNVPRequest(Varien_Object $payment, $method)
    {
		$nvpArray = $this->getNVPArray($payment, $method);
		$nvp = '';
		foreach ($nvpArray as $k => $v) {
			$nvp .= $k . '=' . urlencode($v) . '&';
		}
		$nvp = rtrim($nvp, '&');
        return $nvp;
    }

	private function processPayment($npvStr) {



		if($this->getConfigData('test') == '1')
		{
			$gateway_url=self::PAYMENTMODULE_API_URL_UAT;
		}else
		{
			$gateway_url=self::PAYMENTMODULE_API_URL_LIVE;
		}



		$http = new Varien_Http_Adapter_Curl();
        $config = array('timeout' => 60);
		$http->setConfig($config);


		$http->write(Zend_Http_Client::POST, $gateway_url, '1.1', array(), $npvStr);
		$response = $http->read();




        if ($http->getErrno()) {
            $http->close();
            $this->errorDetails = array(
				'type' => 'CURL',
				'code' => $http->getErrno(),
				'message' => $http->getError()
			);
			return false;
        }
        $http->close();

		$response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

		return($response);
	}

	/**
     * this method is called if we are just authorising
     * a transaction
     */
    public function authorize (Varien_Object $payment, $amount)
    {
				$error = false;
		$payment->setAmount($amount);

		$nvpStr = $this->getNVPArray($payment, $method,'4');


		$response = $this->processPayment($nvpStr);



		if (!$response) {
			$error = Mage::helper('checkoutcom')->__('Gateway request error: %s', $this->errorDetails['message']);
		}
		else {
			$result = @simplexml_load_string($response);
			$gwerror = $result->{'error_text'};

			if(!$gwerror){
				if (!$result) {
					$error = Mage::helper('checkoutcom')->__('Cannot process your payment. Please try again.');
				}
				elseif ($result->{'responsecode'} != '0') {
					$error = Mage::helper('checkoutcom')->__("Cannot process your payment, error: %s (%s). Please try again.", $result->{'result'},$result->{'responsecode'}, $response);
				}
			}else
			{
				$error = Mage::helper('checkoutcom')->__("Cannot process your payment, error: %s. Please try again.", $gwerror, $response);
			}
		}
		if ($error !== false) {
            Mage::throwException($error);
        }
		else {
			$payment->setStatus(self::STATUS_APPROVED);
			$payment->setLastTransId($result->{'tranid'});
		}

		return $this;
    }

    /**
     * this method is called if we are authorising AND
     * capturing a transaction
     */
    public function capture (Varien_Object $payment, $amount)
    {

		$error = false;
		$payment->setAmount($amount);

		$nvpStr = $this->getNVPArray($payment, $method,'1');


		$response = $this->processPayment($nvpStr);



		if (!$response) {
			$error = Mage::helper('checkoutcom')->__('Gateway request error: %s', $this->errorDetails['message']);
		}
		else {
			$result = @simplexml_load_string($response);
			$gwerror = $result->{'error_text'};

			if(!$gwerror){
				if (!$result) {
					$error = Mage::helper('checkoutcom')->__('Cannot process your payment. Please try again.');
				}
				elseif ($result->{'responsecode'} != '0') {
					$error = Mage::helper('checkoutcom')->__("Cannot process your payment, error: %s (%s). Please try again.", $result->{'result'},$result->{'responsecode'}, $response);
				}
			}else
			{
				$error = Mage::helper('checkoutcom')->__("Cannot process your payment, error: %s. Please try again.", $gwerror, $response);
			}
		}
		if ($error !== false) {
            Mage::throwException($error);
        }
		else {
			$payment->setStatus(self::STATUS_APPROVED);
			$payment->setLastTransId($result->{'tranid'});
		}

		return $this;
    }

    /**
     * called if refunding
     */
    public function refund (Varien_Object $payment, $amount)
    {
		Mage::throwException("Refund not Supported.");
		return $this;
    }

    /**
     * called if voiding a payment
     */
    public function void (Varien_Object $payment)
    {
		Mage::throwException("Voi not Supported.");
		return $this;
    }
}