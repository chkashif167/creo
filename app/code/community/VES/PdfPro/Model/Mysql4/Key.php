<?php

class VES_PdfPro_Model_Mysql4_Key extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the pickupfromstore_id refers to the key field in your database table.
        $this->_init('pdfpro/key', 'entity_id');
    }
}