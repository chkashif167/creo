<?php

class VES_PdfPro_Model_Communication_Method_Post extends VES_PdfPro_Model_Communication_Method_Abstract
{
	const SERVER_URL ='http://192.99.35.139/easypdfinvoice/index.php/pdf/process';
	
	public function process($data = array(),$type='invoice',$pdfPro){
		$params = array(
			'data'	=> $pdfPro->encode(json_encode($data),$pdfPro->getApiKey()),
			'api_key'	=> $pdfPro->getApiKey(),
			'version'	=> $pdfPro->getVersion(),
			'type'		=> $type,
			'hash'		=> $pdfPro->getHash(),
			'domain'	=> Mage::getStoreConfig('web/unsecure/base_url'),
		);

		$result = $this->sendRequest($params);
		$result['content']	= $pdfPro->decode($result['content'],$pdfPro->getApiKey());

		$result = new Varien_Object($result);
		Mage::dispatchEvent('ves_pdfpro_pdf_prepare', array('type'=>$type, 'result'=>$result, 'communication'=>'post'));
		return $result->getData();
	}
	
	public function sendRequest($params){	
		foreach($params as $key=>$value){
			$fieldString .= urlencode($key).'='.urlencode($value)."&";
		}
		$fieldString = trim($fieldString, '&');
		$agent= 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:19.0) Gecko/20100101 Firefox/19.0';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fieldString);
		curl_setopt($ch, CURLOPT_URL,self::SERVER_URL);
		$result = curl_exec($ch);
		$result = json_decode($result,true);
		return $result;
	}
}