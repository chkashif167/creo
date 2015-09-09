<?php

class VES_PdfPro_Model_Source_Key
{
	public function toOptionArray()
    {
        $collection = Mage::getModel('pdfpro/key')->getCollection();
        $options 	= array();
        $options[] = array('value'=>'','label'=>'');
        foreach($collection as $option){
        	$options[] = array('value'=>$option->getId(), 'label'=>$option->getApiKey());
        }
        return $options;
    }
}