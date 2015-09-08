<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PdfGenerator
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Mysql4_Pdfgenerator extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Intitialize the collection
     */
    public function _construct()
    {
        $this->_init('eadesign/pdfgenerator', 'pdftemplate_id');
    }
}
