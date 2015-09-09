<?php
class VES_PdfProProcessor_Model_Source_Symbology
{
	
	public function toOptionArray(){
		return array(
		    'BCGcodabar'	=> Mage::helper('pdfproprocessor')->__('Codabar'),
			'BCGcode11'		=> Mage::helper('pdfproprocessor')->__('Code 11'),
			'BCGcode39'		=> Mage::helper('pdfproprocessor')->__('Code 39'),
			'BCGcode39extended'		=> Mage::helper('pdfproprocessor')->__('Code 39 Extended'),
			'BCGcode93'		=> Mage::helper('pdfproprocessor')->__('Code 93'),
			'BCGcode128'	=> Mage::helper('pdfproprocessor')->__('Code 128'),
			'BCGean8'		=> Mage::helper('pdfproprocessor')->__('EAN-8'),
			'BCGean13'		=> Mage::helper('pdfproprocessor')->__('EAN-13'),
			'BCGgs1128'		=> Mage::helper('pdfproprocessor')->__('GS1-128 (EAN-128)'),
			'BCGisbn'		=> Mage::helper('pdfproprocessor')->__('ISBN'),
			'BCGi25'		=> Mage::helper('pdfproprocessor')->__('Interleaved 2 of 5'),
			'BCGs25'		=> Mage::helper('pdfproprocessor')->__('Standard 2 of 5'),
			'BCGmsi'		=> Mage::helper('pdfproprocessor')->__('MSI Plessey'),
			'BCGupca'		=> Mage::helper('pdfproprocessor')->__('UPC-A'),
			'BCGupce'		=> Mage::helper('pdfproprocessor')->__('UPC-E'),
			'BCGupcext2'	=> Mage::helper('pdfproprocessor')->__('UPC Extenstion 2 Digits'),
			'BCGupcext5'	=> Mage::helper('pdfproprocessor')->__('UPC Extenstion 5 Digits'),
			'BCGpostnet'	=> Mage::helper('pdfproprocessor')->__('Postnet'),
			'BCGintelligentmail'	=> Mage::helper('pdfproprocessor')->__('Intelligent Mail'),
			'BCGothercode'	=> Mage::helper('pdfproprocessor')->__('Other Barcode'),
		);
	}
}