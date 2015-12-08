<?php

class Progos_Creomob_ConfigurationController extends Mage_Adminhtml_Controller_Action {
	


	public function indexAction(){
		$this->loadLayout();
		$block = $this->getLayout()->createBlock('core/text', 'configuration-block')->setText('<h1>Creomob Configuration</h1>');
        $this->_addContent($block);
		$this->renderLayout();
	}

	public function showAction(){
		$model = Mage::getModel("progos_creomob/configuration");
		echo get_class($model),'<br>';
		$resource_model = Mage::getResourceModel('progos_creomob/configuration');
		echo get_class($resource_model),'<br>';
		$model->load(1);
		if($model->getId()){
			print_r($model->getData());
		}

		echo '<br>--Collection---<br>';
		$c = Mage::getModel('progos_creomob/configuration')->getCollection();
		var_dump($c->getData());
	}
}