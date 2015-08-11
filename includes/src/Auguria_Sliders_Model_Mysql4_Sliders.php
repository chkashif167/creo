<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Model_Mysql4_Sliders extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('auguria_sliders/sliders', 'slider_id');
    }
    
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
    	
    	$condition = $this->_getWriteAdapter()->quoteInto('slider_id = ?', $object->getId());
    	
    	// Stores
    	$this->_getWriteAdapter()->delete($this->getTable('auguria_sliders/stores'), $condition);
    	
    	// Cms pages
    	$this->_getWriteAdapter()->delete($this->getTable('auguria_sliders/pages'), $condition);
    	
    	// Category ids
    	$this->_getWriteAdapter()->delete($this->getTable('auguria_sliders/categories'), $condition);
    	
    	return parent::_afterDelete($object);
    }
    /**
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('slider_id = ?', $object->getId());
        
        // Stores
        $this->_getWriteAdapter()->delete($this->getTable('auguria_sliders/stores'), $condition);
        foreach ((array)$object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray['slider_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('auguria_sliders/stores'), $storeArray);
        }

        // Cms pages
        $this->_getWriteAdapter()->delete($this->getTable('auguria_sliders/pages'), $condition);
        foreach ((array)$object->getData('pages') as $page) {
            $pageArray = array();
            $pageArray['slider_id'] = $object->getId();
            $pageArray['page_id'] = $page;
            $this->_getWriteAdapter()->insert($this->getTable('auguria_sliders/pages'), $pageArray);
        }
        
        // Category ids
        $this->_getWriteAdapter()->delete($this->getTable('auguria_sliders/categories'), $condition);
        foreach ((array)$object->getData('category_ids') as $category) {
            $categoryArray = array();
            $categoryArray['slider_id'] = $object->getId();
            $categoryArray['category_id'] = $category;
            $this->_getWriteAdapter()->insert($this->getTable('auguria_sliders/categories'), $categoryArray);
        }
        
        return parent::_afterSave($object);
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {

        if (!intval($value) && is_string($value)) {
            $field = 'slider_id';
        }
        return parent::load($object, $value, $field);
    }

    /**
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
        	
        	// Stores
            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('auguria_sliders/stores'))
                ->where('slider_id = ?', $object->getId());
            if ($data = $this->_getReadAdapter()->fetchAll($select)) {
                $storesArray = array();
                foreach ($data as $row) {
                    $storesArray[] = $row['store_id'];
                }
                $object->setData('stores', $storesArray);
            }
            
            // Cms pages
            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('auguria_sliders/pages'))
                ->where('slider_id = ?', $object->getId());
            if ($data = $this->_getReadAdapter()->fetchAll($select)) {
                $pagesArray = array();
                foreach ($data as $row) {
                    $pagesArray[] = $row['page_id'];
                }
                $object->setData('pages', $pagesArray);
            }
            
            // Category ids
            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('auguria_sliders/categories'))
                ->where('slider_id = ?', $object->getId());
            if ($data = $this->_getReadAdapter()->fetchAll($select)) {
                $categoriesArray = array();
                foreach ($data as $row) {
                    $categoriesArray[] = $row['category_id'];
                }
                $object->setData('category_ids', $categoriesArray);
            }
        }
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $select->join(array('wcs' => $this->getTable('auguria_sliders/stores')), $this->getMainTable().'.slider_id = wcs.slider_id')
                    ->where('wcs.store_id in (0, ?) ', $object->getStoreId())
                    ->order('store_id DESC')
                    ->limit(1);
        }
        return $select;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        return $this->_getReadAdapter()->fetchCol(
            $this->_getReadAdapter()->select()
            ->from($this->getTable('auguria_sliders/stores'), 'store_id')
            ->where("{$this->getIdFieldName()} = ?", $id)
        );
    }
}