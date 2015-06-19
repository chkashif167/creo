<?php
class MST_Pdp_Model_Config_Maxsize extends Mage_Core_Model_Config_Data {

    protected function _beforeSave() {
        $maxSize = trim($this->getValue());
        if(is_numeric($maxSize)) {
            $helper = Mage::helper("pdp/upload");
            $fieldsetData = $this->getData('fieldset_data');
            $requestMaxSize = (float) $fieldsetData["upload_max_size"];
            $sizeUnit = $fieldsetData["size_unit"];
            switch($sizeUnit) {
                case 'k' : case 'K' : 
                    $requestMaxSize *= 1024;
                    break;
                case 'm' : case 'M' :
                    $requestMaxSize *= 1048576;
                    break;
            }
            //Compare max size request and upload_max_filesize allowed by server
            $uploadMaxFilesize = $helper->getUploadMaxFileSize("b"); // max file size in byte
            if($requestMaxSize > $uploadMaxFilesize) {
                throw new Exception("Please enter a number equal or less than : " . $helper->getUploadMaxFileSize($sizeUnit) . " " . $sizeUnit);        
            }
        } else {
            throw new Exception("Please enter a valid number in 'Max size allowed' field!");    
        }
        //return false;
        
        return;
        
		//$uploadMaxFileSize = 
        //die($maxSize);
    }

}