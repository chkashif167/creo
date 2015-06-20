<?php
class MST_Pdp_Block_System_Config_Form_Field_Notification extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
      //  $element->setValue(Mage::app()->loadCache('admin_notifications_lastcheck'));
      //  $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
		$main_domain = Mage::helper('pdp')->get_domain( $_SERVER['SERVER_NAME'] );
		if ( $main_domain != 'dev' ) {
		$rakes = Mage::getModel('pdp/act')->getCollection();
		$rakes->addFieldToFilter('path', 'pdp/act/key' );
		$valid = false;
		
			if ( count($rakes) > 0 ) {
				foreach ( $rakes as $rake )  {
					if ( $rake->getExtensionCode() == md5($main_domain.trim(Mage::getStoreConfig('pdp/act/key')) ) ) {
						$valid = true;	
					}
				}
			}
			
			$html = base64_decode('PHAgc3R5bGU9ImNvbG9yOiByZWQ7Ij48Yj5OT1QgVkFMSUQ8L2I+PC9wPjxhIGhyZWY9Imh0dHA6Ly93d3cucHJvZHVjdHNkZXNpZ25lcnByby5jb20vI3ByaWNpbmciIHRhcmdldD0iX2JsYW5rIj5WaWV3IFByaWNlPC9hPjwvYnI+');	
			
			if ( $valid == true ) {
			//if ( count($rakes) > 0 ) {  
				foreach ( $rakes as $rake )  {
					if ( $rake->getExtensionCode() == md5($main_domain.trim(Mage::getStoreConfig('pdp/act/key')) ) ) {
						$html = base64_decode('PGhyIHdpZHRoPSIyODAiPjxiPltEb21haW5Db3VudF0gRG9tYWluIExpY2Vuc2U8L2I+PGJyPjxiPkFjdGl2ZSBEYXRlOiA8L2I+W0NyZWF0ZWRUaW1lXTxicj48YSBocmVmPSJodHRwOi8vd3d3LnByb2R1Y3RzZGVzaWduZXJwcm8uY29tLyNwcmljaW5nIiB0YXJnZXQ9Il9ibGFuayI+VmlldyBQcmljZTwvYT48YnI+');	
						$html = str_replace(array('[DomainCount]','[CreatedTime]'),array($rake->getDomainCount(),$rake->getCreatedTime()),$html);
					}
				}
			}
		} else { 
		$html = base64_decode('PGEgaHJlZj0iaHR0cDovL3d3dy5wcm9kdWN0c2Rlc2lnbmVycHJvLmNvbS8jcHJpY2luZyIgdGFyZ2V0PSJfYmxhbmsiPlZpZXcgUHJpY2U8L2E+PC9icj4=');
		}	
		
		
        return $html;
    }
}