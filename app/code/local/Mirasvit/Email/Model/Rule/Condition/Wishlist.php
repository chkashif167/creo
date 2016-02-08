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



class Mirasvit_Email_Model_Rule_Condition_Wishlist extends  Mirasvit_Email_Model_Rule_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $attributes = array(
            'summary_qty' => Mage::helper('email')->__('Wishlist: Total quantity of products'),
            'summary_count' => Mage::helper('email')->__('Wishlist: Total count of products'),
            'subtotal' => Mage::helper('email')->__('Wishlist: Subtotal'),
            'wishlist_item_duration' => Mage::helper('email')->__('Wishlist Item: Number of passed days since date when a product was added to wishlist'),
        );

        asort($attributes);

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'wishlist_item_duration':
                return 'numeric';
                break;
        }

        return parent::getInputType();
    }

    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();

        $qty = 0;
        $count = 0;
        $subtotal = 0;
        $duration = 0;

        if ($object->getData('wishlist_item_id')) {
            $diff = Mage::getModel('core/date')->gmtTimestamp() - $object->getData('time'); // getData('time') - is a wishlist item's added_at value
            $duration = floor($diff / (60 * 60 * 24));
        }

        if ($object->getData('customer_id')) {
            $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($object->getData('customer_id'));

            foreach ($wishlist->getItemCollection() as $item) {
                $product = $item->getProduct();
                $product->setCustomOptions($item->getOptions());
                $product->prepareCustomOptions();

                $subtotal += $product->getFinalPrice() * $item->getQty();
                $qty      += $item->getQty();
                ++$count;
            }
        }

        $object->setData('summary_qty', $qty)
            ->setData('summary_count', $count)
            ->setData('subtotal', $subtotal)
            ->setData('wishlist_item_duration', $duration);

        $value = $object->getData($attrCode);

        return $this->validateAttribute($value);
    }
}
