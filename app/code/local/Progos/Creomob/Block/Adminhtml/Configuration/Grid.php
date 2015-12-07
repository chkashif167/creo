<?php


class Progos_Creomob_Block_Adminhtml_Configuration_Grid extends Mage_Adminhtml_Block_Widget_Grid {



	protected function _prepareCollection(){
		$collection = Mage::getResourceModel('progos_creomob/configuration_collection');

		$this->setCollection($collection);

		return parent::_prepareCollection(); 
	}

	protected function _prepareColumn(){
		$this->addColumn('entity_id', array(
            'header' => $this->_getHelper()->__('ID'),
            'type' => 'number',
            'index' => 'entity_id',
        ));

        $this->addColumn('created_at', array(
            'header' => $this->_getHelper()->__('Created'),
            'type' => 'datetime',
            'index' => 'created_at',
        ));

        return parent::_prepareColumns();
	}


	protected function _getHelper()
    {
        return Mage::helper('progos_creomob');
    }

}