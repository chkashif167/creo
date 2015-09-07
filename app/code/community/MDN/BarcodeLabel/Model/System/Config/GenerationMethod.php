<?php

class MDN_BarcodeLabel_Model_System_Config_GenerationMethod extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
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

            $methods = $this->getMethods(); //       echo'<pre>'; print_r($standardsLists); echo'</pre>'; die('zo');
            $this->_options = array();
            foreach ($methods as $code => $name){

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
    public function getMethods(){

        // all barcode types shipped by default with Zend Framework.
        $list = array('random' => Mage::helper('BarcodeLabel')->__('Random'),
            'list' => Mage::helper('BarcodeLabel')->__('Predefined list'));

        return $list;
    }

}