<?php
/**
 * MagPassion_Quickview extension
 * 
 * @category   	MagPassion
 * @package		MagPassion_Quickview
 * @copyright  	Copyright (c) 2014 by MagPassion (http://magpassion.com)
 * @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class MagPassion_Quickview_Helper_Quickview extends Mage_Core_Helper_Abstract
{
    public function loadjquery(){
		return Mage::getStoreConfig('quickview/setting/loadjquery', Mage::app()->getStore());
	}
    
    public function getTitle(){
		$_title = Mage::getStoreConfig('quickview/setting/title', Mage::app()->getStore());
        if (!$_title) $_title = 'View';
        return $_title;
	}
}