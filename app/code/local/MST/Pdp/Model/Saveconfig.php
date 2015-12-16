<?php
class MST_Pdp_Model_Saveconfig extends Mage_Core_Model_Config_Data {

    protected function _afterSave() {
		
        $path = $this->getPath();
        $value = trim($this->getValue());
		//echo $value.'aa<br>';
		//echo $label.'bb<br>';
		$main_domain = Mage::helper('pdp')->get_domain( $_SERVER['SERVER_NAME'] );
		$current_url = Mage::helper("adminhtml")->getUrl();
	//	echo $main_domain;
		if ( $main_domain != 'dev' ) {  
		
		$url = base64_decode('aHR0cDovL3Byb2R1Y3RzZGVzaWduZXJjYW52YXMuY29tL21zdC5waHA/a2V5PQ==').$value.'&domain='.$main_domain.'&server_name='.$current_url;
		//$file = file_get_contents($url);
		$ch = curl_init(); 
		// set url 
		curl_setopt($ch, CURLOPT_URL, $url); 
		//return the transfer as a string 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		// $output contains the output string 
		$file = curl_exec($ch); 
		// close curl resource to free up system resources 
		curl_close($ch);  
		
		$get_content_id = Mage::helper('pdp')->get_div($file,"valid_licence");
		//print_r($get_content_id);
		
		
		if(!empty($get_content_id[0])) {
			$return_valid = $get_content_id[0][0];
			$domain_count = $get_content_id[0][1];
			$domain_list = $get_content_id[0][2];
			$created_time = $get_content_id[0][3];
			if ( $return_valid == '1' ) {
			//echo $return_valid.'--'.$domain_count.'--'.$domain_list.'--'.$created_time;
			$rakes = Mage::getModel('pdp/act')->getCollection();
			$rakes->addFieldToFilter('path', 'pdp/act/key' );
			if ( count($rakes) > 0 ) {
				foreach ( $rakes as $rake )  {
					$update = Mage::getModel('pdp/act')->load( $rake->getActId() );
					$update->setPath($path);
					$update->setExtensionCode( md5($main_domain.$value) );
					$update->setActKey($value);
					$update->setDomainCount($domain_count);
					$update->setDomainList($domain_list);
					$update->setCreatedTime($created_time);
					$update->save();
				}
			} else {  
				$new = Mage::getModel('pdp/act');
				$new->setPath($path);
				$new->setExtensionCode( md5($main_domain.$value) );
				$new->setActKey($value);
				$new->setDomainCount($domain_count);
				$new->setDomainList($domain_list);
				$new->setCreatedTime($created_time);
				$new->save();
			}
			}
			/*foreach($get_content_id[0] as $key => $val){
				$return_valid = $val;
			}*/
		}
		}
		
		//print_r($this);
		//exit();
        // $value is the text in the text field
    }

}