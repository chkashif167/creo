<?php
/**
 * VES_PdfPro_Adminhtml_NotifyController
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */

class VES_PdfPro_Adminhtml_NotifyController extends Mage_Adminhtml_Controller_Action
{
	public function hidemessageAction(){
		
		$messageFile 	= Mage::getBaseDir('media').DS.'ves_pdfpro'.DS.'message.txt';
		if(file_exists($messageFile)){
			$info = file_get_contents($messageFile);
			$info = json_decode(base64_decode($info),true);
			$info['hide']	= true;
			$date 		= Mage::getModel('core/date')->date('Y-m-d');
			try{
				$fp		= fopen($messageFile, 'w');
				fwrite($fp, base64_encode(json_encode($info)));
				fclose($fp);
			}catch(Exception $e){
				
			}
		}
		
		$result = array('error'=>false);
		$this->getResponse()->setBody(json_encode($result));
	}
}