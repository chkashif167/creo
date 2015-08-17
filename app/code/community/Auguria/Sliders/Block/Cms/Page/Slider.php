<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Block_Cms_Page_Slider extends Auguria_Sliders_Block_Abstract
{
	public function getSlides()
	{
		$slides = parent::getSlides();
		$tableName = Mage::getSingleton('core/resource')->getTableName('auguria_sliders/pages');
		$slides->getSelect()
					->join( array('sp'=>$tableName),
									'main_table.slider_id = sp.slider_id', array('sp.*'))
					->where('sp.page_id = ?', $this->getPageId());
		return $slides;
	}
	
	public function getPageId()
	{
		try {
			return (int)Mage::getBlockSingleton('cms/page')->getPage()->getPageId();
		}
		catch (Exception $e) {
			return 0;
		}
	}
}