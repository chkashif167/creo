<?php
class VES_PdfProProcessor_Model_Source_Rotation
{
	public function toOptionArray(){
		return array(
		    '0'		=> Mage::helper('pdfproprocessor')->__('No rotation'),
			'90'	=> Mage::helper('pdfproprocessor')->__('90 degree clockwise'),
			'180'	=> Mage::helper('pdfproprocessor')->__('180 degree clockwise'),
			'270'	=> Mage::helper('pdfproprocessor')->__('270 degree clockwise'),
		);
	}
}