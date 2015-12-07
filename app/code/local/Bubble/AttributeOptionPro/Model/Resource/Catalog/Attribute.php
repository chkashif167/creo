<?php
/**
 * @category    Bubble
 * @package     Bubble_AttributeOptionPro
 * @version     1.1.4
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_AttributeOptionPro_Model_Resource_Catalog_Attribute
    extends Mage_Catalog_Model_Resource_Attribute
{
    protected function _saveOption(Mage_Core_Model_Abstract $object)
    {
        $option = $object->getOption();
        if (is_array($option)) {
            $write = $this->_getWriteAdapter();
            $optionTable        = $this->getTable('attribute_option');
            $optionValueTable   = $this->getTable('attribute_option_value');
            $stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(true)
                ->load();

            if (isset($option['value'])) {
                $attributeDefaultValue = array();
                if (!is_array($object->getDefault())) {
                    $object->setDefault(array());
                }

                foreach ($option['value'] as $optionId => $values) {
                    $intOptionId = (int) $optionId;
                    if (!empty($option['delete'][$optionId])) {
                        if ($intOptionId) {
                            $condition = $write->quoteInto('option_id=?', $intOptionId);
                            $write->delete($optionTable, $condition);
                        }

                        continue;
                    }

                    if (!$intOptionId) {
                        $data = array(
                           'attribute_id'       => $object->getId(),
                           'sort_order'         => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                           'image'              => isset($option['image'][$optionId]) ? $option['image'][$optionId] : '',
                           'additional_image'   => isset($option['additional_image'], $option['additional_image'][$optionId]) ? $option['additional_image'][$optionId] : '',
                        );
                        $write->insert($optionTable, $data);
                        $intOptionId = $write->lastInsertId();
                    } else {
                        $data = array(
                           'sort_order'         => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                           'image'              => isset($option['image'][$optionId]) ? $option['image'][$optionId] : '',
                           'additional_image'   => isset($option['additional_image'], $option['additional_image'][$optionId]) ? $option['additional_image'][$optionId] : '',
                        );
                        $write->update($optionTable, $data, $write->quoteInto('option_id=?', $intOptionId));
                    }

                    if (in_array($optionId, $object->getDefault())) {
                        if ($object->getFrontendInput() == 'multiselect') {
                            $attributeDefaultValue[] = $intOptionId;
                        } else if ($object->getFrontendInput() == 'select') {
                            $attributeDefaultValue = array($intOptionId);
                        }
                    }

                    // Default value
                    if (!isset($values[0])) {
                        Mage::throwException(Mage::helper('eav')->__('Default option value is not defined.'));
                    }

                    foreach ($stores as $store) {
                        if (Mage::helper('bubble_aop')->isStoreHidden($store->getId())) {
                            continue;
                        }
                        $where = array(
                            $write->quoteInto('option_id = ?', $intOptionId),
                            $write->quoteInto('store_id = ?', $store->getId())
                        );
                        $write->delete($optionValueTable, $where);
                        if (isset($values[$store->getId()]) && (!empty($values[$store->getId()]) || $values[$store->getId()] == "0")) {
                            $data = array(
                                'option_id' => $intOptionId,
                                'store_id'  => $store->getId(),
                                'value'     => $values[$store->getId()],
                            );
                            $write->insert($optionValueTable, $data);
                        }
                    }
                }

                $write->update($this->getMainTable(), array(
                    'default_value' => implode(',', $attributeDefaultValue)
                ), $write->quoteInto($this->getIdFieldName() . '=?', $object->getId()));
            }
        }

        return $this;
    }

    protected function _saveStoreLabels(Mage_Core_Model_Abstract $object)
    {
        $storeLabels = $object->getStoreLabels();
        if (is_array($storeLabels)) {
            $adapter = $this->_getWriteAdapter();
            foreach ($storeLabels as $storeId => $label) {
                if (Mage::helper('bubble_aop')->isStoreHidden($storeId)) {
                    continue;
                }
                if ($object->getId()) {
                    $condition = array(
                        'attribute_id = ?' => $object->getId(),
                        'store_id = ?' => $storeId,
                    );
                    $adapter->delete($this->getTable('eav/attribute_label'), $condition);
                }
                if ($storeId == 0 || !strlen($label)) {
                    continue;
                }
                $bind = array (
                    'attribute_id' => $object->getId(),
                    'store_id'     => $storeId,
                    'value'        => $label
                );
                $adapter->insert($this->getTable('eav/attribute_label'), $bind);
            }
        }

        return $this;
    }
}