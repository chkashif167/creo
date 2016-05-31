<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('amcheckoutfees/rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $addressCondition  = Mage::getModel('amcheckoutfees/rule_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();

        $attributes = array();
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = array('value' => 'amcheckoutfees/rule_condition_address|' . $code, 'label' => $label);
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'amcheckoutfees/rule_condition_product_subselect', 'label' => Mage::helper('salesrule')->__('Products subselection')),
            array('label' => Mage::helper('salesrule')->__('Conditions combination'), 'value' => $this->getType()),
            array('label' => Mage::helper('salesrule')->__('Cart Attribute'), 'value' => $attributes),
        )
        );

        $additional = new Varien_Object();
        Mage::dispatchEvent('salesrule_rule_condition_combine', array('additional' => $additional));
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}