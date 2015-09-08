<?php

class MDN_BarcodeLabel_Model_System_Config_BarcodeStandards extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    
     protected $_options;
     
    /**
    * give options to the select fields
    * 
    * @param type $isMultiselect
    * @return type 
    */
    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options)
        {
            $this->getAllOptions();
        }
        return $this->_options;
    }
    
    /**
     * get standards for select field
     * 
     * @return type 
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            
            // get the list of available standards for generate barcode
            $standardsLists = $this->getStandards(); //       echo'<pre>'; print_r($standardsLists); echo'</pre>'; die('zo');
            $this->_options = array();
            foreach ($standardsLists as $code => $name){
                
                $this->_options[] = 
                array(
                        'value' => $code,
                        'label' => $name,
                    );
            }
        }
        return $this->_options;
    }

    /**
     * return array that contain ["code type"] => "name's code"
     * 
     * @return type 
     */
    public function getStandards(){
        
    // all barcode types shipped by default with Zend Framework.
    $list = array('Code25' => 'Code 25',
        'Code25interleaved' => 'Code 25 interleaved',
        'Code39' => 'Code39',
        'Code128'=> 'Code 128',
        'Ean2' => 'Ean 2',
        'Ean5' => 'Ean 5',
        'Ean8' => 'Ean 8',
        'Ean13' => 'Ean 13',
        'Identcode' => 'Identcode',
        'Itf14' => 'ITF-14',
        'Leitcode' => 'Leitcode',
        'Planet' => 'Planet',
        'Postnet' => 'Postnet',
        'Royalmail' => 'Royal Mail',
        'Upca' => 'Upc-A',
        'Upce' => 'Upc-E');
        
      return $list;  
    }
    
}