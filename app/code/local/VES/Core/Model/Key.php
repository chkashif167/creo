<?php

class VES_Core_Model_Key extends Mage_Core_Model_Abstract
{
	const VNECOMS_XMLRPC		= 'http://www.vnecoms.com/api/xmlrpc/';
    const VNECOMS_API_USERNAME	= 'vnecoms_license_key';
    const VNECOMS_API_PASSWORD	= 'VnEcoms123)(*';
	const ENCODED_KEY	= '3132cf3739a48ae62cabbc56b5e899f0';
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('ves_core/key');
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
	}
	
	/**
	 * Encode text
	 * @param string $code
	 * @param string $key
	 * @return string
	 */
	public function encode($code,$key){
		$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $code, MCRYPT_MODE_CBC, md5(md5($key))));
		return $code;
	}
	
    /**
     * Get license key inforamtion
     * @param string $licenseKey
     */
    public function getKeyInfo($licenseKey){
    	$licenseKey = $this->encode($licenseKey, self::ENCODED_KEY);
    	$client 			= new Zend_XmlRpc_Client(self::VNECOMS_XMLRPC);
		$client->getHttpClient()->setConfig(array('timeout'=>1200));
		$session 			= $client->call('login', array(self::VNECOMS_API_USERNAME, self::VNECOMS_API_PASSWORD));
		$result 			= $client->call('call', array($session, 'license.get_license_info', array($licenseKey)));
		$client->call('endSession', array($session));
		return $result;
    }
}