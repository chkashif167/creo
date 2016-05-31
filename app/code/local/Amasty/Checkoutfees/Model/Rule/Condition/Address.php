<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Model_Rule_Condition_Address extends Mage_Rule_Model_Condition_Abstract
{
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    public function getDefaultOperatorInputByType()
    {
        $op             = parent::getDefaultOperatorInputByType();
        $op['string'][] = '{%';
        $op['string'][] = '%}';

        return $op;
    }

    public function getDefaultOperatorOptions()
    {
        $op       = parent::getDefaultOperatorOptions();
        $op['{%'] = Mage::helper('rule')->__('starts from');
        $op['%}'] = Mage::helper('rule')->__('ends with');

        return $op;
    }

    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'package_value':
            case 'package_weight':
            case 'package_qty':
                return 'numeric';

            case 'country_id':
            case 'region_id':
            case 'payment_method':
            case 'shipping_method':
                return 'select';
        }

        return 'string';
    }

    public function getOperatorSelectOptions()
    {
        $operators = $this->getOperatorOption();
        if ($this->getAttribute() == 'street') {
            $operators = array(
                '{}'  => Mage::helper('rule')->__('contains'),
                '!{}' => Mage::helper('rule')->__('does not contain'),
                '{%'  => Mage::helper('rule')->__('starts from'),
                '%}'  => Mage::helper('rule')->__('ends with'),
            );
        }

        $type           = $this->getInputType();
        $opt            = array();
        $operatorByType = $this->getOperatorByInputType();
        foreach ($operators as $k => $v) {
            if (!$operatorByType || in_array($k, $operatorByType[$type])) {
                $opt[] = array('value' => $k, 'label' => $v);
            }
        }

        return $opt;
    }

    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'country_id':
            case 'region_id':
            case 'payment_method':
            case 'shipping_method':
                return 'select';
        }

        return 'text';
    }

    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
                    break;

                case 'region_id':
                    $options = Mage::getModel('adminhtml/system_config_source_allregion')->toOptionArray();
                    break;

                case 'shipping_method':
                    $options = Mage::getModel('amcheckoutfees/options_allshippingmethods')->toOptionArray();
                    break;

                case 'payment_method':
                    $options = Mage::getModel('amcheckoutfees/options_allpaymentmethods')->toOptionArray();
                    break;

                default:
                    $options = array();
            }
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    public function loadAttributeOptions()
    {
        parent::loadAttributeOptions();
        $attributes = array(
            'amcheckoutfees_amount' => Mage::helper('salesrule')->__('Checkoutfees amount'),
            'subtotal'              => Mage::helper('salesrule')->__('Subtotal'),
            'subtotal_incl_tax'     => Mage::helper('salesrule')->__('Subtotal Including Taxes'),
            'grand_total'           => Mage::helper('salesrule')->__('Grand Total'),
            'tax_amount'            => Mage::helper('salesrule')->__('Taxes Amount'),
            'discount_amount'       => Mage::helper('salesrule')->__('Discount Amount'),
            'total_qty'             => Mage::helper('salesrule')->__('Total Items Quantity'),
            'weight'                => Mage::helper('salesrule')->__('Total Weight'),
            'payment_method'        => Mage::helper('salesrule')->__('Payment Method'),
            'shipping_method'       => Mage::helper('salesrule')->__('Shipping Method'),
            'shipping_amount'       => Mage::helper('salesrule')->__('Shipping Amount'),
            'postcode'              => Mage::helper('salesrule')->__('Shipping Postcode'),
            'street'                => Mage::helper('salesrule')->__('Shipping Street'),
            'region'                => Mage::helper('salesrule')->__('Shipping Region'),
            'region_id'             => Mage::helper('salesrule')->__('Shipping State/Province'),
            'country_id'            => Mage::helper('salesrule')->__('Shipping Country'),
            'city'                  => Mage::helper('salesrule')->__('Shipping City'),
            'email'                 => Mage::helper('salesrule')->__('Customer Email'),
            'telephone'             => Mage::helper('salesrule')->__('Customer Telephone'),

        );

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function validateAttribute($validatedValue)
    {
        if (is_object($validatedValue)) {
            return false;
        }

        if (is_string($validatedValue)) {
            $validatedValue = strtoupper($validatedValue);
        }

        /**
         * Condition attribute value
         */
        $value = $this->getValueParsed();
        if (is_string($value)) {
            $value = strtoupper($value);
        }

        /**
         * Comparison operator
         */
        $op = $this->getOperatorForValidate();

        // if operator requires array and it is not, or on opposite, return false
        if ($this->_isArrayOperatorType() xor is_array($value)) {
            return false;
        }

        $result = false;
        switch ($op) {
            case '{%':
                if (!is_scalar($validatedValue)) {
                    return false;
                } else {
                    $result = substr($validatedValue, 0, strlen($value)) == $value;
                }
                break;
            case '%}':
                if (!is_scalar($validatedValue)) {
                    return false;
                } else {
                    $result = substr($validatedValue, -strlen($value)) == $value;
                }
                break;
            default:
                return parent::validateAttribute($validatedValue);
                break;
        }

        return $result;

    }

    /**
     * Check if value should be array
     *
     * Depends on operator input type
     *
     * @return bool
     */
    protected function _isArrayOperatorType()
    {
        $ret = false;
        if (method_exists($this, 'isArrayOperatorType')) {
            $ret = $this->isArrayOperatorType();
        } else {
            $op  = $this->getOperator();
            $ret = ($op === '()' || $op === '!()');
        }

        return $ret;
    }
}