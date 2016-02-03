<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_SearchLandingPage_Model_Resource_Page extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('searchlandingpage/page', 'page_id');
    }

    protected function loadStoreIds(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('searchlandingpage/page_store'))
            ->where('page_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['store_id'];
            }
            $object->setData('store_ids', $array);
        }

        return $object;
    }

    protected function saveStoreIds($object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('page_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('searchlandingpage/page_store'), $condition);

        foreach ((array) $object->getData('store_ids') as $id) {
            $objArray = array(
                'page_id' => $object->getId(),
                'store_id' => $id,
            );
            $this->_getWriteAdapter()->insert(
                $this->getTable('searchlandingpage/page_store'), $objArray);
        }

        return $object;
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassDelete()) {
            $this->loadStoreIds($object);
        }

        return parent::_afterLoad($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassStatus()) {
            $this->saveStoreIds($object);
        }

        return parent::_afterSave($object);
    }
}
