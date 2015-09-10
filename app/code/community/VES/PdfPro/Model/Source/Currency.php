<?php

class VES_PdfPro_Model_Source_Currency
{
	public function toOptionArray()
    {
        return array(
        	Zend_Currency::STANDARD => Mage::helper('pdfpro')->__('Standard'),
        	Zend_Currency::LEFT 	=> Mage::helper('pdfpro')->__('Left'),
        	Zend_Currency::RIGHT 	=> Mage::helper('pdfpro')->__('Right'), 
        );
    }
}