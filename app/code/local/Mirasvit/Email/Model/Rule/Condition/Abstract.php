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


class Mirasvit_Email_Model_Rule_Condition_Abstract extends Mage_Rule_Model_Condition_Abstract
{
    protected function _prepareValueOptions()
    {
    }

    public function getValueSelectOptions()
    {
        $this->_prepareValueOptions();

        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'shipping_method':
                    $options = Mage::getModel('adminhtml/system_config_source_shipping_allmethods')
                        ->toOptionArray();
                    break;
                case 'payment_method':
                    $options = Mage::getModel('adminhtml/system_config_source_payment_allowedmethods')
                        ->toOptionArray();
                    break;
                default:
                    $options = array();
            }
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method':
            case 'payment_method':
                return 'select';
            case 'updated_at':
            case 'created_at':
                return 'date';
        }

        return parent::getInputType();
    }

    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method':
            case 'payment_method':
                return 'select';
            case 'updated_at':
            case 'created_at':
                return 'date';
        }

        return parent::getValueElementType();
    }

    public function getValueElement()
    {
        $element = parent::getValueElement();
            switch ($this->getAttribute()) {
                case 'updated_at':
                case 'created_at':
                    $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                    break;
            }

        return $element;
    }

    public function getValueAfterElementHtml()
    {
        $html = '';

        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
            break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="'.$image.'" alt="" class="v-middle rule-chooser-trigger" title="'.__('Open Chooser').'" /></a>';
        }

        return $html;
    }

    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'category_ids':
            case 'sku':
                $url = 'adminhtml/promo_widget/chooser'
                    .'/attribute/'.$this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/'.$this->getJsFormObject();
                }
            break;
        }

        return $url !== false ? Mage::helper('adminhtml')->getUrl($url) : '';
    }

    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
            case 'updated_at':
            case 'created_at':
                return true;
            break;
        }

        return false;
    }

    public function validateAttribute($validatedValue)
    {
        $op = $this->getOperatorForValidate();
        $value = $this->getValueParsed();

        if ($op == '{}' || $op == '!{}') {
            $result = false;
            if (is_array($validatedValue) && is_scalar($value)) {
                foreach ($validatedValue as $item) {
                    if (stripos($item, $value) !== false) {
                        $result = true;
                        break;
                    }
                }
            }

            if ($op == '!{}') {
                $result = !$result;
            }

            return $result;
        }

        return parent::validateAttribute($validatedValue);
    }
}