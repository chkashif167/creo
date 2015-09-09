<?php

class VES_PdfPro_Model_Processors_Easypdf extends Mage_Core_Model_Abstract
{
	public function process($apiKey, $datas, $type){
		$pdfPro = new PdfPro($apiKey);
		$methodConfig 	= Mage::getStoreConfig('pdfpro/config/communication_method');
		$methodDatas	= Mage::getConfig()->getNode('global/easypdf_communication_method')->asArray();
		if(isset($methodDatas[$methodConfig])){
			$method	= array(
				'title' => $methodDatas[$methodConfig]['title'],
				'model' => Mage::getModel($methodDatas[$methodConfig]['model']),
			);
			return $pdfPro->getPDF($datas,$type,$method);
		}else{
			throw new Mage_Core_Exception('You need to select the communication method by go to EasyPDF -> Configuration');
		}
	}
}