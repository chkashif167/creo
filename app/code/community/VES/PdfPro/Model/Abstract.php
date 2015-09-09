<?php
/**
 * VES_PdfPro_Model_Abstract
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Model_Abstract extends Varien_Object
{
	
	/**
     * Get All formated date for givent date
     * @param string $date
     * @return Varien_Object
     */
	public function getFormatedDate($date,$type = null){
    	$dateFormated = new Varien_Object(array(
    		'full' 		=> Mage::app()->getLocale()->date(strtotime($date))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_FULL)),
    		'long' 		=> Mage::app()->getLocale()->date(strtotime($date))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_LONG)),
    		'medium' 	=> Mage::app()->getLocale()->date(strtotime($date))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)),
    		'short' 	=> Mage::app()->getLocale()->date(strtotime($date))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)),
    	));
    	if($type) return $dateFormated->getData($type);
    	return $dateFormated;
    }
    /**
     * Get address data by address object
     * @param object $address
     * @return Varien_Object
     */
    public function getAddressData($address){
    	if(!$address) return array();
    	$data = $address->getData();
    	foreach($data as $key=>$value){
    		if(is_object($value)) unset($data[$key]);
    	}
    	$data['country_name'] 	= Mage::app()->getLocale()->getCountryTranslation($data['country_id']);
    	$data['formated']		= $address->getFormated(true);
    	$data					= new Varien_Object($data);
    	Mage::dispatchEvent('ves_pdfpro_data_prepare_after',array('source'=>$data,'model'=>$address,'type'=>'address'));
    	return $data;
    }

	/**
	 * Process Source Data
	 * @param Object $sourceData
	 * @param string $currencyCode
	 * @param string $baseCurrencyCode
	 */
	public function process($sourceData,$currencyCode,$baseCurrencyCode=null){
		$baseCurrencyCode = $baseCurrencyCode?$baseCurrencyCode:Mage::app()->getStore()->getBaseCurrency()->getCode();
		foreach($sourceData as $key=>$value){
			if(is_object($value)){unset($sourceData[$key]); continue;}
			if(in_array($key, $this->getPriceAttributes())){
				if($value) $sourceData[$key]	= Mage::helper('pdfpro')->currency($value,$currencyCode);
			}
			
			if(in_array($key, $this->getBasePriceAttributes())){
				if($value) $sourceData[$key]	= Mage::helper('pdfpro')->currency($value,$baseCurrencyCode);
			}
		}
		
		return $sourceData;
	}
	
	public function getCustomerData(Mage_Customer_Model_Customer $customer){
		if(!$customer->getId()){return array('customer_is_guest'=>1);}
		$data = $customer->getData();
		if(isset($data['dob'])) $data['customer_dob']	= $this->getFormatedDate($data['dob']);
		if(isset($data['gender'])) $data['gender'] = $this->getOptionById($data['gender'])->getValue();
		$data	= new Varien_Object($data);
		Mage::dispatchEvent('ves_pdfpro_data_prepare_after',array('source'=>$data,'model'=>$customer,'type'=>'customer'));
		return $data->getData();
	}
	public function getOptionById($optionId){
		$store = Mage::app()->getStore();
    	$option = Mage::getResourceModel('eav/entity_attribute_option_collection')
						->setPositionOrder('asc')
						->addFieldToFilter('main_table.option_id',$optionId)
						->setStoreFilter()
						->load()
						->getFirstItem();
		return $option;
    }
	public function getPriceAttributes(){
		return array();
	}
	
	public function setTranslationByStoreId($storeId){
		if(!Mage::getStoreConfig('pdfpro/config/detect_language')) return;
		if($storeId){
	    	Mage::app()->getLocale()->emulate($storeId);
	        /*Mage::app()->setCurrentStore($storeId);*/
		}
	}
	
	public function revertTranslation(){
		if(!Mage::getStoreConfig('pdfpro/config/detect_language')) return;
		Mage::app()->getLocale()->revert();
	}
}