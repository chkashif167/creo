<?php

class VES_Core_Helper_Core extends Mage_Core_Helper_Data
{
	public function __construct(){
		if(Mage::registry('ves_core_files_changed') === null){
			/*check if files is changed here*/
			/*Mage::register('ves_core_files_changed', 1);*/
		}
	}
}