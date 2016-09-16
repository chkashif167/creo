<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Model_Rule_Condition_Product_Subselect extends Mage_SalesRule_Model_Rule_Condition_Product_Subselect
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('amcheckoutfees/rule_condition_product_subselect')
             ->setValue(null);
    }

    public function loadAttributeOptions()
    {
        $hlp = Mage::helper('salesrule');
        $this->setAttributeOption(array(
                'qty'                     => $hlp->__('total quantity'),
                'base_row_total'          => $hlp->__('total amount excl. tax'),
                'base_row_total_incl_tax' => $hlp->__('total amount incl. tax'),
                'row_weight'              => $hlp->__('total weight'),
            )
        );

        return $this;
    }

    /**
     * validate
     *
     * @param Varien_Object $object Quote
     *
     * @return boolean
     */
    public function validate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            return false;
        }

        $attr  = $this->getAttribute();
        $total = 0;
        if ($object->getAllItems()) {
            $validIds = array();
            foreach ($object->getAllItems() as $item) {


                if ($item->getProduct()->getTypeId() == 'configurable') {
                    $item->getProduct()->setTypeId('skip');
                }

                //can't use parent here
                if (Mage_SalesRule_Model_Rule_Condition_Product_Combine::validate($item)) {
                    $itemParentId = $item->getParentItemId();
                    if (is_null($itemParentId)) {
                        $validIds[] = $item->getItemId();
                    } else {
                        if (in_array($itemParentId, $validIds)) {
                            continue;
                        } else {
                            $validIds[] = $itemParentId;
                        }
                    }


                    $total += $item->getData($attr);
                }

                if ($item->getProduct()->getTypeId() === 'skip') {
                    $item->getProduct()->setTypeId('configurable');
                }
            }
        }

        return $this->validateAttribute($total);
    }
}
