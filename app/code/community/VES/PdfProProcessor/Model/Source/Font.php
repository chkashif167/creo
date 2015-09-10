<?php
class VES_PdfProProcessor_Model_Source_Font
{
	public function toOptionArray(){
		return array(
		    '0'			=> Mage::helper('pdfproprocessor')->__('No Label'),
			'Arial.ttf'	=> Mage::helper('pdfproprocessor')->__('Arial'),
		);
	}
}