<?php
class VES_Core_Block_Adminhtml_Key_Grid_Renderer_License extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Store
{
    public function render(Varien_Object $row)
    {
    	$value	= $row->getData($this->getColumn()->getIndex());
    	$licenseInfo = unserialize(Mage::getModel('ves_core/key')->decode($value,VES_Core_Model_Key::ENCODED_KEY));
    	
    	$result = '';
    	if($licenseInfo && is_array($licenseInfo)){
	    	$result .= '<label style="width:100px;float: left;">'.Mage::helper('ves_core')->__('Extension').':</label> <strong>'.$licenseInfo['item_name'].'</strong><br />';
	    	$result .= '<label style="width:100px;float: left;">'.Mage::helper('ves_core')->__('License Type').':</label> '.$licenseInfo['type'].'<br />';
	    	//$createdAt = Mage::app()->getLocale()->date(strtotime($licenseInfo['created_at']))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM));
	    	//$result .= '<label style="width:100px;float: left;">'.Mage::helper('ves_core')->__('Created At').':</label> '.$createdAt.'<br />';
	    	//$expirationDate = $licenseInfo['expiry_at']?Mage::app()->getLocale()->date(strtotime($licenseInfo['expiry_at']))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)):'n/a';
	    	//$result .= '<label style="width:100px;float: left;">'.Mage::helper('ves_core')->__('Expiration date').':</label> '.$expirationDate.'<br />';
	    	$result .= '<label style="width:100px;float: left;">'.Mage::helper('ves_core')->__('Domains').':</label> '.implode(',', $licenseInfo['domains']).'<br />';
	    	$result .= $licenseInfo['status']==0?'<span style="color: #FF0000;">'.Mage::helper('ves_core')->__('Your license key has been expired.').'</span>':($licenseInfo['status']==3?'<span style="color: #FF0000;">'.Mage::helper('ves_core')->__('Your license key has been suspended.').'</span>':'');
    	}else{
    		$result = '<span style="color: #FF0000;">'.Mage::helper('ves_core')->__('Your license information is not valid.').'</span>';
    	}
    	return $result;
    }
    
}
