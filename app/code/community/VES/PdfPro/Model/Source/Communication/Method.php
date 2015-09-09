<?php

class VES_PdfPro_Model_Source_Communication_Method
{
	public function toOptionArray()
    {
        $methods = Mage::getConfig()->getNode('global/easypdf_communication_method')->asArray();
    	
        $options 	= array();
        $options[] = array('value'=>'', 'label'=>'');
        foreach($methods as $key => $method){
        	$options[] = array('value'=>$key, 'label'=>$method['title']);
        }
        return $options;
    }
}