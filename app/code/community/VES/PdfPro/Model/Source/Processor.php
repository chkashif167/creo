<?php

class VES_PdfPro_Model_Source_Processor
{
	public function toOptionArray()
    {
    	$processors = Mage::getConfig()->getNode('global/easypdf_processors')->asArray();
    	
        $options 	= array();
        foreach($processors as $key => $processor){
        	$options[] = array('value'=>$processor['model'], 'label'=>$processor['title']);
        }
        return $options;
    }
}