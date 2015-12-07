<?php


class Progos_Creomob_Model_Resource_Configuration_Collection 
	extends Mage_Core_Model_Resource_Db_Collection_Abstract{

		protected function _construct(){
			parent::_construct();

			$this->_init('progos_creomob/configuration','progos_creomob_configuration');
		}
}