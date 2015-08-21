<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Model_Mysql4_Sliders_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('auguria_sliders/sliders');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('slider_id', 'frontend_subject');
    }

    /**
     * Add filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return Auguria_Contact_Model_Mysql4_Contacts_Collection
     */
	public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }
        
        $this->getSelect()->join(array('wcs' => $this->getTable('auguria_sliders/stores')), 'main_table.slider_id = wcs.slider_id',array())
        				->where('wcs.store_id in (?) ', $withAdmin ? array(0, $store) : $store);
        				
        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}