<?php
/**
 * PDF Pro Class
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class PdfPro
{
    const PDF_PRO_WSDL			= 'r0zxpJlqSHdBKEjlCP9Xng0w/dk2htuUHo0zldCFd4BNGEqzBMVq1Gv2DtTN+RLhZTJU6q8jXyhBhspOkYMw1A==';
    const PDF_PRO_WSDL_DEV		= '/BjMOeoCH8YfyHoPc6j5J+63/2ahVFdeg1v6UIY/1/xFk3sQe6OnDvhTHqetoKYOtwtT9KYsAwU360adBUekdw==';
    const PDF_PRO_XMLRPC		= 'r0zxpJlqSHdBKEjlCP9Xng0w/dk2htuUHo0zldCFd4BkqJyD87fw8QBTsUrwFThlX0xsYPD56yJVLB2n7gKvfw==';
    const PDF_PRO_API_USERNAME	= 'm6oKw/bnKJTI9A+PCkt8nMJMJgQbMxI/sGDQpRxwCbk=';
    const PDF_PRO_API_PASSWORD	= 'wSljQQuWzjU0K8xBeQ159Pb9B/h9P4qeVIZkM+TVz1Q=';
    
    protected $_api_key;
    /**
     * Checksum
     */
    public function getHash(){
    	return md5(file_get_contents(__FILE__));
    }
    public function getApiKey(){
    	return $this->_api_key;
    }
    /**
	 * Decode encoded text
	 * @param string $encoded
	 * @param string $key
	 * @return string
	 */
	public function decode($encoded,$key){
		$code = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encoded), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
		return $code;
		//return $this->_unCompress($code);
	}
	
	/**
	 * Encode text
	 * @param string $code
	 * @param string $key
	 * @return string
	 */
	public function encode($code,$key){
		//$code = $this->_compress($code);
		$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $code, MCRYPT_MODE_CBC, md5(md5($key))));
		return $code;
	}
	/**
	 * Compress a string
	 * @param string $code
	 * @return string
	 */
	protected function _compress($code){
		return gzdeflate(gzcompress($code,9),9);
	}
	/**
	 * Uncompress a string
	 * @param string $code
	 * @return string
	 */
	protected function _unCompress($code){
		return gzuncompress(gzinflate($code));
	}
    
    public function __construct($apiKey=null){
    	$this->_api_key = $apiKey;
    	return $this;
    }
    
    /**
     * Gets the detailed PDF Pro version information
     *
     * @return array
     */
    public static function getVersionInfo()
    {
    	return array(
    			'major'     => '1',
    			'minor'     => '2',
    			'revision'  => '0',
    			'patch'     => '3',
    			'stability' => '1',
    			'number'    => '',
    	);
    }
    
    /**
     * Gets the current PDF Pro version string
     *
     * @return string
     */
    public static function getVersion()
    {
    	$i = self::getVersionInfo();
    	return trim("{$i['major']}.{$i['minor']}.{$i['revision']}" . ($i['patch'] != '' ? ".{$i['patch']}" : "")
    	. "-{$i['stability']}{$i['number']}", '.-');
    }
    /**
     * Get version of PDF Pro from Server
     */
	public function getServerVersion()
    {
    	if(class_exists('SoapClient')){
	    	$client 			= new PdfProSoapClient($this->decode(self::PDF_PRO_WSDL, '5e6bf967aab429405f5855145e6e0fa7'));
	    	$client->__setTimeout(1200);
	    	$session 			= $client->login($this->decode(self::PDF_PRO_API_USERNAME, '5e6bf967aab429405f5855145e6e0fa7'), $this->decode(self::PDF_PRO_API_PASSWORD, '5e6bf967aab429405f5855145e6e0fa7'));
	    	$result 			= $client->call($session, 'pdfpro.getVersion',array());
	    	$client->endSession($session);
    	}else if(class_exists('Zend_XmlRpc_Client')){
    		$client 			= new Zend_XmlRpc_Client($this->decode(self::PDF_PRO_XMLRPC, '5e6bf967aab429405f5855145e6e0fa7'));
    		$client->getHttpClient()->setConfig(array('timeout'=>1200));
    		$session 			= $client->call('login', array($this->decode(self::PDF_PRO_API_USERNAME, '5e6bf967aab429405f5855145e6e0fa7'), $this->decode(self::PDF_PRO_API_PASSWORD, '5e6bf967aab429405f5855145e6e0fa7')));
    		$result 			= $client->call('call', array($session, 'pdfpro.getVersion', array()));
    		$client->call('endSession', array($session));
    	}else{
    		$result = array('success'=>false, 'msg'=>"Your website does not support for PDF Pro");
    	}
    	return $result;
    }
	/**
     * Get version of PDF Pro from Server
     */
	public function getMessage()
    {
    	if(class_exists('SoapClient')){
	    	$client 			= new PdfProSoapClient($this->decode(self::PDF_PRO_WSDL, '5e6bf967aab429405f5855145e6e0fa7'));
	    	$client->__setTimeout(1200);
	    	$session 			= $client->login($this->decode(self::PDF_PRO_API_USERNAME, '5e6bf967aab429405f5855145e6e0fa7'), $this->decode(self::PDF_PRO_API_PASSWORD, '5e6bf967aab429405f5855145e6e0fa7'));
	    	$result 			= $client->call($session, 'pdfpro.getMessage',array());
	    	$client->endSession($session);
    	}else if(class_exists('Zend_XmlRpc_Client')){
    		$client 			= new Zend_XmlRpc_Client($this->decode(self::PDF_PRO_XMLRPC, '5e6bf967aab429405f5855145e6e0fa7'));
    		$client->getHttpClient()->setConfig(array('timeout'=>1200));
    		$session 			= $client->call('login', array($this->decode(self::PDF_PRO_API_USERNAME, '5e6bf967aab429405f5855145e6e0fa7'), $this->decode(self::PDF_PRO_API_PASSWORD, '5e6bf967aab429405f5855145e6e0fa7')));
    		$result 			= $client->call('call', array($session, 'pdfpro.getMessage', array()));
    		$client->call('endSession', array($session));
    	}else{
    		$result = array('success'=>false, 'msg'=>"Your website does not support for PDF Pro");
    	}
    	return $result;
    }
    /**
     * Send data to server return content of PDF invoice
     * @param array $data
     * @param string $type
     * @return array
     */
    public function getPDF($data = array(),$type='invoice',$method=null){
    	if(!$method || !is_array($method) || !isset($method['model'])){
    		return array('success'=>false, 'msg'=>"You need to select the communication method");
    	}
    	$methodModel = $method['model'];    	
    	$result = $methodModel->process($data,$type,$this);
    	return $result;
    }
}
if(class_exists('SoapClient')){
	class PdfProSoapClient extends SoapClient
	{
	    private $timeout;
	
	    public function __setTimeout($timeout)
	    {
	        if (!is_int($timeout) && !is_null($timeout))
	        {
	            throw new Exception("Invalid timeout value");
	        }
	        $this->timeout = $timeout;
	    }
	
	    public function __doRequest($request, $location, $action, $version, $one_way = FALSE)
	    {
	        if (!$this->timeout)
	        {
	            $response = parent::__doRequest($request, $location, $action, $version, $one_way);
	        }
	        else
	        {
	            $curl = curl_init($location);
	            curl_setopt($curl, CURLOPT_VERBOSE, FALSE);
	            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	            curl_setopt($curl, CURLOPT_POST, TRUE);
	            curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
	            curl_setopt($curl, CURLOPT_HEADER, FALSE);
	            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
	            curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
	
	            $response = curl_exec($curl);
	            if (curl_errno($curl))
	            {
	                throw new Exception(curl_error($curl));
	            }
	            curl_close($curl);
	        }
	        if (!$one_way)
	        {
	            return ($response);
	        }
	    }
	}
}