<?php
class VES_PdfProProcessor_Model_Source_Eec
{
	public function toOptionArray(){
		return array(
			"L"=>Mage::helper('pdfproprocessor')->__('L - Smallest'),
			'M'=>Mage::helper('pdfproprocessor')->__('M'),
			'Q'=>Mage::helper('pdfproprocessor')->__('Q'),
			'H'=>Mage::helper('pdfproprocessor')->__('H - Best'),
		);
	}
}