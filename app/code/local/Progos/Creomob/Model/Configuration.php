<?php

class Progos_Creomob_Model_Configuration extends Mage_Core_Model_Abstract {


	protected function _construct(){

		$this->_init('progos_creomob/configuration');

	}


	protected function _beforeSave(){
		parent::_beforeSave();

		$this->_updateTimeStamps();

		return $this;
	}

	protected function _updateTimeStamps(){
		$timestamp = now();
        $this->setUpdatedAt($timestamp);

        if ($this->isObjectNew()) {
            $this->setCreatedAt($timestamp);
        }

        return $this;
	}



}