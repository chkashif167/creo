<?php

class VES_PdfPro_Model_Source_Attach
{
	const ATTACH_TYPE_NO		= 0;
	const ATTACH_TYPE_BOTH		= 1;
	const ATTACH_TYPE_CUSTOMER	= 2;
	const ATTACH_TYPE_ADMIN		= 3;
	
	public function toOptionArray()
    {
        return array(
        	self::ATTACH_TYPE_NO		=>Mage::helper('pdfpro')->__('No'),
	        self::ATTACH_TYPE_CUSTOMER	=>Mage::helper('pdfpro')->__('Customer'),
	        self::ATTACH_TYPE_ADMIN		=>Mage::helper('pdfpro')->__('Admin'),
	        self::ATTACH_TYPE_BOTH		=>Mage::helper('pdfpro')->__('Customer and Admin'),
        );
    }
}