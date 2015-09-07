<?php

/**
 * Description of PdfGenerator
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Pdfgenerator extends Mage_Core_Model_Abstract
{

    CONST PRODUCTTEMPLATE = 'productpdftemplate';
    CONST ORDERTEMPLATE = 'orderpdftemplate';
    CONST INVOICETEMPLATE = 'invoicepdftemplate';
    CONST CMEMOTEMPLATE = 'cmemopdftemplate';
    CONST SHIPPMENTTEMPLATE = 'shippmentpdftemplate';

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    public function _construct()
    {
        $this->_init('eadesign/pdfgenerator');
    }

    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            self::STATUS_ENABLED => Mage::helper('pdfgenerator')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('pdfgenerator')->__('Disabled'),
        ));

        return $statuses->getData();
    }
}
