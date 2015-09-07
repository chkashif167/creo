<?php

class MDN_BarcodeLabel_Block_Adminhtml_System_Config_Version extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return type 
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        
        $html = '';
        
        try {
            $modules = Mage::getConfig()->getNode('modules')->children();
            if( !empty($modules)){
                $modulesArray = (array)$modules;
                $html = $modulesArray['MDN_BarcodeLabel']->version;
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }

        $ulrDoc = 'http://www.boostmyshop.com/english/ElasticSupport/Front/';
        $html .= '<span style="float:right;margin-right:25px;"><i><a href="'.$ulrDoc.'" title='.$this->__('Online Documentation').'" target="_new">'.$this->__('Online Documentation').'</a></i><span>';
        
        return $html;
    }
}