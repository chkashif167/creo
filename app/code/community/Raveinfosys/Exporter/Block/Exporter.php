<?php
class Raveinfosys_Exporter_Block_Exporter extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getExporter()     
     { 
        if (!$this->hasData('exporter')) {
            $this->setData('exporter', Mage::registry('exporter'));
        }
        return $this->getData('exporter');
        
    }
}