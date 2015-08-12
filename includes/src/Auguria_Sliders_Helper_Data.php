<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Helper_Data extends Mage_Catalog_Helper_Data
{
	public function getIsActiveOptionArray()
	{
		return array(
				1	=> $this->__('Enabled'),
				0	=> $this->__('Disabled')
		);
	}
}