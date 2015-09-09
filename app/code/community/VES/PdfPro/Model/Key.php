<?php

class VES_PdfPro_Model_Key extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('pdfpro/key');
    }
}