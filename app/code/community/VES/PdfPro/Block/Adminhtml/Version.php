<?php
/**
 * VES_PdfPro_Block_Adminhtml_Version
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Block_Adminhtml_Version extends Mage_Adminhtml_Block_Template
{
	/**
	 * Write version information from server to local file
	 * @param string $versionFile
	 */
	public function writeVersionFile($versionFile){
		$date 			= Mage::getModel('core/date')->date('Y-m-d');
		$serverVersion 	= Mage::helper('pdfpro')->getServerVersion();
		try{
			$fp				= fopen($versionFile, 'w');
			fwrite($fp, base64_encode(json_encode(array('date'=>$date,'version'=>$serverVersion))));
			fclose($fp);
		}catch(Exception $e){
			
		}
		return $serverVersion;
	}
	/**
	 * Get version of PDF Pro From server
	 */
	public function getServerVersion(){
		$versionFile 	= Mage::getBaseDir('media').DS.'ves_pdfpro'.DS.'version.txt';
		if(!file_exists($versionFile)){
			//$serverVersion 	= $this->writeVersionFile($versionFile);
			return false;
		}else{
			$date 			= Mage::getModel('core/date')->date('Y-m-d');
			$info = file_get_contents($versionFile);
			$info = json_decode(base64_decode($info),true);
			$serverVersion = $info['version'];
		}
		return $serverVersion;
	}
	/**
	 * get Message from server
	 * @param string $versionFile
	 */
	public function writeServerMessage($messageFile){
		$message	= Mage::helper('pdfpro')->getServerMessage();
		$date 		= Mage::getModel('core/date')->date('Y-m-d');
		try{
			$fp		= fopen($messageFile, 'w');
			fwrite($fp, base64_encode(json_encode(array('date'=>$date,'message'=>$message))));
			fclose($fp);
		}catch(Exception $e){
			
		}
		return $message;
	}
	
	/**
	 * Get Message from server
	 */
	public function getServerMessage(){
		$messageFile 	= Mage::getBaseDir('media').DS.'ves_pdfpro'.DS.'message.txt';
		if(!file_exists($messageFile)){
			//$message 	= $this->writeServerMessage($messageFile);
			return false;
		}else{
			$date 			= Mage::getModel('core/date')->date('Y-m-d');
			$info = file_get_contents($messageFile);
			$info = json_decode(base64_decode($info),true);
			if(isset($info['hide']) && $info['hide']) return false;
			$message = $info['message'];
		}
		return $message;
	}
	
	public function canDisplayNotice(){
		return Mage::getStoreConfig('pdfpro/notification/notice');
	}
	
	public function canDisplayNews(){
		return Mage::getStoreConfig('pdfpro/notification/news');
	}
	
	public function canDisplayDefaultApiKeyMsg(){
		$defaultKey = Mage::getStoreConfig('pdfpro/config/default_key');
		if(!$defaultKey) return true;
		$keyModel = Mage::getModel('pdfpro/key')->load($defaultKey);
		return !($keyModel->getApiKey());
	}
	
	public function checkLocalPdfProcessor(){
		$config = Mage::app()->getConfig()->getModuleConfig('VES_PdfProProcessor');
		return /*$config->is('active') && */(Mage::getStoreConfig('pdfpro/config/processor') != 'pdfpro/processors_easypdf');
	}
	
	public function checkFolderWriteAble(){
		return is_writable(Mage::getBaseDir('media').DS.'ves_pdfpro');
	}
}