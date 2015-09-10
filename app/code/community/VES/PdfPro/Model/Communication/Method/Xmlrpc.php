<?php

class VES_PdfPro_Model_Communication_Method_Xmlrpc extends Varien_Object
{
	public function process($data = array(),$type='invoice',$pdfPro){
		$client 			= new Zend_XmlRpc_Client($pdfPro->decode(PdfPro::PDF_PRO_XMLRPC, '5e6bf967aab429405f5855145e6e0fa7'));
		$client->getHttpClient()->setConfig(array('timeout'=>1200));
		$session 			= $client->call('login', array($pdfPro->decode(PdfPro::PDF_PRO_API_USERNAME, '5e6bf967aab429405f5855145e6e0fa7'), $pdfPro->decode(PdfPro::PDF_PRO_API_PASSWORD, '5e6bf967aab429405f5855145e6e0fa7')));
		$result 			= $client->call('call', array($session, 'pdfpro.getPdf', array($pdfPro->encode(json_encode($data),$pdfPro->getApiKey()), $pdfPro->getApiKey(),$pdfPro->getVersion(),$type,$pdfPro->getHash(),Mage::getStoreConfig('web/unsecure/base_url'))));
		$result['content']	= $pdfPro->decode($result['content'],$pdfPro->getApiKey());
		$client->call('endSession', array($session));
		
    	$result = new Varien_Object($result);
    	Mage::dispatchEvent('ves_pdfpro_pdf_prepare',array('type'=>$type, 'result'=>$result, 'communication'=>'xmlrpc'));
    	return $result->getData();
	}
}