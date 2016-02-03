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



class Mirasvit_SearchIndex_Model_Resource_Index extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('searchindex/index', 'index_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract  $object)
    {
        $object->validate();
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        if ($object->getAttributes() && is_array($object->getAttributes())) {
            $object->setAttributesSerialized(serialize($object->getAttributes()));
        }

        if ($object->getProperties() && is_array($object->getProperties())) {
            $object->setPropertiesSerialized(serialize($object->getProperties()));
        }

        if ($object->getData('attributes_serialized') != $object->getOrigData('attributes_serialized')) {
            $object->setStatus(2);
        }

        if ($object->getData('properties_serialized') != $object->getOrigData('properties_serialized')) {
            $object->setStatus(2);
        }

        if (!$object->getId() && !$object->getIndexInstance()->isAllowMultiInstance()) {
            //check if this index already exists
            $collection = Mage::getModel('searchindex/index')->getCollection()
                ->addFieldToFilter('index_code', $object->getIndexCode());
            if ($collection->count() > 0) {
                Mage::throwException('The index for current content type already exists');
            }
        }

        return parent::_beforeSave($object);
    }
}
