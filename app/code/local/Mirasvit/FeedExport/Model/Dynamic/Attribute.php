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
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Model_Dynamic_Attribute extends Mage_Core_Model_Abstract
{
    static $_attr = array();

    protected function _construct()
    {
        $this->_init('feedexport/dynamic_attribute');
    }

    public function getValue($product)
    {
        $patternModel = Mage::getSingleton('feedexport/feed_generator_pattern');
        $conditions   = $this->getConditions();
        $value        = null;

        foreach ($conditions as $key => $condition) {
            if ($key == 'default') {
                continue;
            }

            $valid = true;
            if (isset($condition['attribute'])) {
                foreach ($condition['attribute'] as $indx => $attrCode) {
                    $attrPattern = '{'.$attrCode.'}';
                    $attrValue = $patternModel->getPatternValue($attrPattern, 'product', $product);

                    $options = $this->getOptions($attrCode);

                    if (isset($options[$condition['value'][$indx]])) {
                        $condition['value'][$indx] = $options[$condition['value'][$indx]];
                    }

                    $validator = Mage::getModel('feedexport/dynamic_attribute_validator');
                    $validator->setValue(trim($condition['value'][$indx]))
                        ->setOperator($condition['condition'][$indx]);

                    if (!$validator->validateAttribute(trim($attrValue))) {
                        $valid = false;
                    }
                }
            }

            if ($valid) {
                if (@$condition['output_type'] == 'attribute') {
                    $value = $patternModel->getPatternValue('{'.$condition['value_attribute'].'}', 'product', $product);;
                } else {
                    $value = $patternModel->getPatternValue($condition['value_pattern'], 'product', $product);
                }
                break;
            }
        }

        if ($value === null) {
            $default = @$conditions['default'];
            if ($default['output_type'] == 'attribute') {
                $value = $patternModel->getPatternValue('{'.$default['value_attribute'].'}', 'product', $product);;
            } else {
                $value = $patternModel->getPatternValue($default['value_pattern'], 'product', $product);
            }
        }

        return $value;
    }

    public function getOptions($code)
    {
        if (!isset(self::$_attr[$code])) {
            $result    = array();
            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $code);
            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);
                if ($attribute->getFrontendInput() == 'boolean') {
                    foreach ($options as $key => $value) {
                        $result[$value['value']] = $value['value'];
                    }
                } else {
                    foreach ($options as $key => $value) {
                        $result[$value['value']] = $value['label'];
                    }
                }
            }
            self::$_attr[$code] = $result;
        }

        return self::$_attr[$code];
    }
}