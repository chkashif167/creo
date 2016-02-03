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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Advr_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $variables = array();

    public function setVariable($key, $value)
    {
        $variable = Mage::getModel('core/variable');
        $variable = $variable->loadByCode('advr_' . $key);

        $value = serialize($value);

        $variable->setPlainValue($value)
            ->setHtmlValue(Mage::getSingleton('core/date')->gmtTimestamp())
            ->setName($key)
            ->setCode('advr_' . $key)
            ->save();

        return $variable;
    }

    public function getVariable($key, $force = false)
    {
        if ($force || !isset($this->variables[$key])) {
            $variable = Mage::getModel('core/variable')->loadByCode('advr_' . $key);

            $this->variables[$key] = unserialize($variable->getPlainValue());
        }

        return $this->variables[$key];
    }

    public function saveCache($key, $value)
    {
        $value = serialize($value);

        Mage::app()->saveCache($value, 'advr_' . $key, array('CONFIG'), 100000000);

        return $this;
    }

    public function loadCache($key)
    {
        $value = Mage::app()->loadCache('advr_' . $key);

        if ($value !== false) {
            $value = unserialize($value);
        }

        return $value;
    }

    public function getAttributeOptionHash($attrCode)
    {
        $options = $this->loadCache($attrCode);

        if ($options === false) {
            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attrCode);
            $options = array();
            if ($attribute && $attribute->usesSource() && $attribute->getFrontendModel() == '' && Mage::getModel($attribute->getSourceModel())) {
                $allOptions = $attribute->getSource()->getAllOptions(false);
                foreach ($allOptions as $opt) {
                    if (is_scalar($opt['value'])) {
                        $options[$opt['value']] = $opt['label'];
                    }
                }
            }

            $this->saveCache($attrCode, $options);
        }


        return $options;
    }
}
