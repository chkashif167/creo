<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Model_Adminhtml_System_Config_Source_Cms_Page
{

    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
        	$collection = Mage::getResourceModel('cms/page_collection');
        	if ($collection && $collection->count()>0) {
        		foreach ($collection as $item) {
	            	$this->_options[] = array(
	                	'value'=> $item->getData('page_id'),
	            		'label'=> $item->getData('title'));
        		}
        	}
        }
        return $this->_options;
    }

}
