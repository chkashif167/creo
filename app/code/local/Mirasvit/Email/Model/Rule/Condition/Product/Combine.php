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



class Mirasvit_Email_Model_Rule_Condition_Product_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('email/rule_condition_product_combine');
    }

    public function getNewChildSelectOptions()
    {
        $productCondition = Mage::getModel('email/rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $pAttributes = array();
        $iAttributes = array();
        foreach ($productAttributes as $code => $label) {
            if (strpos($code, 'quote_item_') === 0) {
                $iAttributes[] = array('value' => 'email/rule_condition_product|'.$code, 'label' => $label);
            } else {
                $pAttributes[] = array('value' => 'email/rule_condition_product|'.$code, 'label' => $label);
            }
        }

        $conditions = array();//parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array(
                'label' => Mage::helper('catalog')->__('Conditions Combination'),
                'value' => 'email/rule_condition_product_combine',
            ),
            // array(
            //     'label' => Mage::helper('catalog')->__('Cart Item Attribute'),
            //     'value' => $iAttributes
            // ),
            array(
                'label' => Mage::helper('catalog')->__('Product Attribute'),
                'value' => $pAttributes,
            ),
        ));

        return $conditions;
    }

    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}
