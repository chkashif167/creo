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


class Mirasvit_Email_Model_Rule_Condition_Cart extends Mirasvit_Email_Model_Rule_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $attributes = array(
            'summary_qty'    => Mage::helper('email')->__('Cart: Total quantity of products'),
            'summary_count'  => Mage::helper('email')->__('Cart: Total count of products'),
            'subtotal'       => Mage::helper('email')->__('Cart: Subtotal'),
        );

        asort($attributes);

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();

        if ($object->getData('quote_id')) {
            $quote = Mage::getModel('sales/quote')->setSharedStoreIds(array_keys(Mage::app()->getStores()))
                ->load($object->getData('quote_id'));

            $qty         = 0;
            $count       = 0;

            foreach ($quote->getItemsCollection() as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());

                $qty += $item->getQty();
                $count += 1;
            }

            $object->setData('summary_qty', $qty)
                ->setData('summary_count', $count)
                ->setData('subtotal', $quote->getSubtotal());
        } else {
            $object->setData('summary_qty', 0)
                ->setData('summary_count', 0)
                ->setData('subtotal', 0);
        }

        $value = $object->getData($attrCode);

        return $this->validateAttribute($value);
    }
}
