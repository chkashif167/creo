<?php 

/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Tagproducts
 * @copyright   Copyright (c) 2014 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Tagproducts_Block_Layer_View extends Mage_Catalog_Block_Layer_View {
		
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();	        
    }
    
	/**
	 * Get layer object
	 * @return Mage_Catalog_Model_Layer
	 */
	public function getLayer()
	{ 
		return Mage::getSingleton('tagproducts/layer');
	}
}
?>