<?php
/**
 * @category   Apptrian
 * @package    Apptrian_Minify
 * @author     Apptrian
 * @copyright  Copyright (c) 2015 Apptrian (http://www.apptrian.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Apptrian_Minify_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	/**
	 * Returns extension version.
	 *
	 * @return string
	 */
	public function getExtensionVersion()
	{
		return (string) Mage::getConfig()->getNode()->modules->Apptrian_Minify->version;
	}
    
}