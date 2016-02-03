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


class Mirasvit_Email_Model_Rule_Condition_Product_Subselect
    extends Mirasvit_Email_Model_Rule_Condition_Product_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('email/rule_condition_product_subselect')
            ->setValue(null);
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().
        Mage::helper('email')->__("If %s products in cart/order matching these conditions:",
            $this->getAggregatorElement()->getHtml());

        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }

    public function validate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            return false;
        }

        $this->setValue(1);
        $collection = null;

        if ($object->getData('quote_id')) {
            $quote = Mage::getModel('sales/quote')
                ->setSharedStoreIds(array_keys(Mage::app()->getStores()))
                ->load($object->getData('quote_id'));

            $collection = $quote->getItemsCollection();
        } elseif ($object->getData('order_id')) {
            $order = Mage::getModel('sales/order')->load($object->getData('order_id'));

            $collection = $order->getItemsCollection();
        }
        
        if ($collection) {
            $total = 0;
            $count = count($collection);
            foreach ($collection as $item) {
                if (parent::validate($item)) {
                    $total++;
                }
            }

            if ($this->getAggregator() == 'any') {
                return $total > 0;
            } else {
                return $total == $count;
            }
        }

        return true;
    }
}
