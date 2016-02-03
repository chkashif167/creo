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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Email_Model_Rule_Condition_Product extends Mage_SalesRule_Model_Rule_Condition_Product
{
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes = array_merge($attributes, array(
            'is_in_stock' => Mage::helper('email')->__('Stock Availability'),
            'type_id' => Mage::helper('email')->__('Product Type'),
        ));
    }

    protected function _prepareValueOptions()
    {
        parent::_prepareValueOptions();
        // Check that both keys exist. Maybe somehow only one was set not in this routine, but externally.
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }

        // Get array of select options. It will be used as source for hashed options
        $selectOptions = null;
        if ($this->getAttribute() === 'is_in_stock') {
            $selectOptions = array();
            $options = Mage::getSingleton('cataloginventory/source_stock')->toOptionArray();
            foreach ($options as $option) {
                $selectOptions[$option['value']] = $option;
            }
        } elseif ($this->getAttribute() === 'type_id') {
            $selectOptions = Mage::getSingleton('catalog/product_type')->getAllOptions();
        }

        // Set new values only if we really got them
        if ($selectOptions !== null) {
            // Overwrite only not already existing values
            if (!$selectReady) {
                $this->setData('value_select_options', $selectOptions);
            }
            if (!$hashedReady) {
                $hashedOptions = array();
                foreach ($selectOptions as $o) {
                    if (is_array($o)) {
                        if (is_array($o['value'])) {
                            continue; // We cannot use array as index
                        }
                        $hashedOptions[$o['value']] = $o['label'];
                    }
                }
                $this->setData('value_option', $hashedOptions);
            }
        }

        return $this;
    }

    public function getInputType()
    {
        if ($this->getAttribute() === 'is_in_stock' ||
            $this->getAttribute() === 'type_id'
        ) {
            return 'select';
        }

        return parent::getInputType();
    }

    public function getValueElementType()
    {
        if ($this->getAttribute() === 'is_in_stock' ||
            $this->getAttribute() === 'type_id'
        ) {
            return 'select';
        }

        return parent::getValueElementType();
    }
}
