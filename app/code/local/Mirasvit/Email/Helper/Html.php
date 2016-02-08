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


class Mirasvit_Email_Helper_Html extends Mage_Core_Helper_Abstract
{
    public function getTemplateSelect($name, $value = null, $extra = array())
    {
        $values = Mage::helper('emaildesign')->getTemplates();
        
        return $this->_getSelect($name, $value, $extra, $values);
    }

    public function getHourSelect($name, $value = null, $extra = array())
    {
        $values = array();
        for ($i = 0; $i < 24; $i++) {
            $values[$i] = $i;
        }

        return $this->_getSelect($name, $value, $extra, $values);
    }

    public function getMinuteSelect($name, $value = null, $extra = array())
    {
        $values = array();
        for ($i = 0; $i < 60; $i++) {
            $values[$i] = $i;
        }

        return $this->_getSelect($name, $value, $extra, $values);
    }

    public function getTypeSelect($name, $value = null, $extra = array())
    {
        $values = array(
            'after' => Mage::helper('email')->__('after'),
            'at'    => Mage::helper('email')->__('at')
        );

        return $this->_getSelect($name, $value, $extra, $values);
    }

    protected function _getSelect($name, $value, $extra, $values)
    {
        $element = new Varien_Data_Form_Element_Select();
        $element
            ->setForm(new Varien_Object())
            ->setValue($value)
            ->setName($name)
            ->addData($extra)
            ->setValues($values);

        return $element->getElementHtml();
    }
}