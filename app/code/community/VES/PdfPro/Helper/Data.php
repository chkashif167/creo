<?php
/**
 * VES_PdfPro_Helper_Data
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
include_once Mage::getBaseDir('code').'/community/VES/PdfPro/Model/PdfPro.php';
class VES_PdfPro_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * Init Pdf by given invoice data
     * @param array $invoiceData
     */
	public function initPdf($datas = array(),$type='invoice'){
		$processorConfig 	= Mage::getStoreConfig('pdfpro/config/processor');
        $processor 			= Mage::getModel($processorConfig);
		$apiKey 			= $this->getDefaultApiKey();
		return $processor->process($apiKey, $datas, $type);
	}
	
	/**
     * Convert and format price value for given currency code
     *
     * @param   float|int $value
     * @param   string $code;
     * @return  string
     */
	public function currency($value, $code='USD',$baseCode=null){
		$precision	= Mage::getStoreConfig('pdfpro/config/number_format')!==''?Mage::getStoreConfig('pdfpro/config/number_format'):2;
		$value 		= $value?$value:0;
		$position 	= intval(Mage::getStoreConfig('pdfpro/config/currency_position'));
		$position?$position:Zend_Currency::STANDARD;
		
		return Mage::app()->getLocale()->currency($code)->toCurrency($value,array('precision'=>$precision,'position'=>$position));
	}
	/**
	 * Get version of PDF Pro
	 */
	public function getVersion(){
		return PdfPro::getVersion();
	}
	/**
	 * Get messages from EasyPdfInvoice.com
	 */
	public function getServerMessage(){
		$apiKey 	= $this->getDefaultApiKey();
		$pdfPro 	= new PdfPro($apiKey);
		return 	$pdfPro->getMessage();
	}
	/**
     * Get version of PDF Pro from Server
     */
	public function getServerVersion(){
		$apiKey 	= $this->getDefaultApiKey();
		$pdfPro 	= new PdfPro($apiKey);
		return 	$pdfPro->getServerVersion();
	}
	/**
	 * Get API Key by Store ID and Customer Group ID
	 * @param int $storeId
	 * @param int $groupId
	 * @return string
	 */
	public function getApiKey($storeId, $groupId){
		$keyCollection = Mage::getModel('pdfpro/key')->getCollection();
		$keyCollection->getSelect()->where("FIND_IN_SET('".$storeId."', store_ids) OR FIND_IN_SET('0', store_ids)")
									->where("FIND_IN_SET('".$groupId."', customer_group_ids)")
									->order('priority ASC')
		;
		
		$apiKey 	= $keyCollection->count()?$keyCollection->getFirstItem()->getApiKey():$this->getDefaultApiKey();
		$apiKeyObj 	= new Varien_Object(array('api_key'=>$apiKey,'store_id'=>$storeId,'group_id'=>$groupId));
		Mage::dispatchEvent('ves_pdfpro_apikey_prepare',array('obj'=>$apiKeyObj));
		$apiKey 	= $apiKeyObj->getApiKey();
		return $apiKey;
	}
	/**
	 * Get the default API Key
	 */
	public function getDefaultApiKey(){
		$defaultApiKey = Mage::getStoreConfig('pdfpro/config/default_key');
		return $defaultApiKey?Mage::getModel('pdfpro/key')->load($defaultApiKey)->getApiKey():false;
	}
	
	/**
	 * Get file name from givent type of PDF
	 * @param string $type
	 */
	public function getFileName($type='invoice',$model = false){
		$fileName = Mage::getStoreConfig('pdfpro/filename_format/'.$type);
		$dateTimeFormatArr = array('$dd','$EEE','$MM','$MMM','$y','$yy','$HH','$mm','$ss');
		$timestamp = Mage::getModel('core/date')->timestamp();
		foreach($dateTimeFormatArr as $dateTimeFormat){
			$fileName = str_replace($dateTimeFormat, Mage::app()->getLocale()->date($timestamp)->toString(trim($dateTimeFormat,'$')), $fileName);
		}
		if($model){$fileName = str_replace('$ID',$model->getIncrementId(),$fileName);}
		return $fileName;
	}
	/**
	 * Check a module is installed or not
	 */
	public function isEnableModule($module){
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules;
		if(!isset($modulesArray[$module])) return false;
		
		return $modulesArray[$module]->is('active');
	}
	
	/**
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    public function formatKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey);
        return $urlKey;
    }
}