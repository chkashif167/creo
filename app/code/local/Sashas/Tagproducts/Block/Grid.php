<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Tagproducts
 * @copyright   Copyright (c) 2014 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Tagproducts_Block_Grid extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {
 
	protected function _prepareLayout()
	{
		parent::_prepareLayout();	
		$widget_params=$this->getData();	 
		Mage::register('sashas_tagproducts_widget_params',$widget_params);
		if ($widget_params['show_layered']){
			$block=$this->getLayout()->createBlock('tagproducts/layer_view')->setTemplate('catalog/layer/view.phtml');		 
			$this->getLayout()->getBlock('left')->append($block);
		}
		 
		return $this;
	}
	
	 
	
	 protected function _toHtml()
	{
		$block_html=$this->getLayout()->createBlock('tagproducts/list')->setTemplate('catalog/product/list.phtml')->toHtml(); 	 
		return $block_html;
	} 
	
 
}
?>