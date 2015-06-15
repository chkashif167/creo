<?php 
/**
 * MagPassion_Quickview extension
 * 
 * @category   	MagPassion
 * @package		MagPassion_Quickview
 * @copyright  	Copyright (c) 2014 by MagPassion (http://magpassion.com)
 * @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Quickview page block
 *
 * @category	MagPassion
 * @package		MagPassion_Quickview
 * @author MagPassion.com
 */
class MagPassion_Quickview_Block_Page extends Mage_Core_Block_Template{
	/**
	 * initialize
	 * @access public
	 * @return void
	 * @author MagPassion.com
	 */
 	public function __construct(){
		parent::__construct();
	}
	/**
	 * prepare the layout
	 * @access protected
	 * @author MagPassion.com
	 */
	protected function _prepareLayout(){
		parent::_prepareLayout();
		
	}
	/**
	 * get the pager html
	 * @access public
	 * @return string
	 * @author MagPassion.com
	 */
	public function getPagerHtml(){
		return $this->getChildHtml('pager');
	}
}