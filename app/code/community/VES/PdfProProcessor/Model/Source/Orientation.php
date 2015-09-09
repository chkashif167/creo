<?php
class VES_PdfProProcessor_Model_Source_Orientation
{
	public function toOptionArray(){
		return array(
			'portrait'=> Mage::helper('pdfproprocessor')->__('Portrait'),
			'landscape'=> Mage::helper('pdfproprocessor')->__('Landscape'),
		);
	}
}