<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Block_Catalog_Category_Slider extends Auguria_Sliders_Block_Abstract
{
	public function getSlides()
	{
		$slides = parent::getSlides();
		/* Join auguria_sliders_pages and filter by page id */
		$tableName = Mage::getSingleton('core/resource')->getTableName('auguria_sliders/categories');
		$slides->getSelect()
					->join( array('sc'=>$tableName),
									'main_table.slider_id = sc.slider_id', array('sc.*'))
					->where('sc.category_id = ?', $this->getCategoryId());
		return $slides;
	}
	
	public function getCategoryId()
	{
		if (Mage::registry('current_category')) {
			return Mage::registry('current_category')->getId();
		}
		return 0;
	}
}