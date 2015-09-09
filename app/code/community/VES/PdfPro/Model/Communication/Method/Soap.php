<?php

class VES_PdfPro_Model_Communication_Method_Soap extends VES_PdfPro_Model_Communication_Method_Abstract
{
	public function process($data = array(),$type='invoice',$pdfPro){
		if(class_exists('SoapClient')){
			$client 			= new PdfProSoapClient($pdfPro->decode(PdfPro::PDF_PRO_WSDL, '5e6bf967aab429405f5855145e6e0fa7'));
	    	$client->__setTimeout(1200);
	    	$session 			= $client->login($pdfPro->decode(PdfPro::PDF_PRO_API_USERNAME, '5e6bf967aab429405f5855145e6e0fa7'), $pdfPro->decode(PdfPro::PDF_PRO_API_PASSWORD, '5e6bf967aab429405f5855145e6e0fa7'));
	    	$result 			= $client->call($session, 'pdfpro.getPdf',array($pdfPro->encode(json_encode($data),$pdfPro->getApiKey()),$pdfPro->getApiKey(),$pdfPro->getVersion(),$type,$pdfPro->getHash(),Mage::getStoreConfig('web/unsecure/base_url')));
	    	$result['content']	= $pdfPro->decode($result['content'],$pdfPro->getApiKey());
	    	$client->endSession($session);
	    	$result = new Varien_Object($result);
	    	Mage::dispatchEvent('ves_pdfpro_pdf_prepare', array('type'=>$type, 'result'=>$result, 'communication'=>'soap'));
	    	return $result->getData();
		}
		throw new Mage_Core_Exception(Mage::helper('pdfpro')->__('Your server does not support for SOAP please install SOAP or use other communication method.'));
	}
}