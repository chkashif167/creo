<?php
class MST_Pdp_Block_Adminhtml_Tabs_Pdpdesign extends Mage_Adminhtml_Block_Widget {
	public function __construct() {
		parent::__construct ();
		$this->setTemplate ( 'pdp/product/pdpdesign.phtml' );
	}
}