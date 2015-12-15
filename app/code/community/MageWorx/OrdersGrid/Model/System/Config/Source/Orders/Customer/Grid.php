<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_System_Config_Source_Orders_Customer_Grid
{

    /**
     * @param bool|false $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect=false)
    {
        /** @var MageWorx_OrdersGrid_Helper_Data $helper */
        $helper = Mage::helper('mageworx_ordersgrid');

        $options = array(
            array('value'=>'increment_id', 'label'=> Mage::helper('customer')->__('Order #')),
            array('value'=>'created_at', 'label'=> Mage::helper('customer')->__('Purchase On')),
            array('value'=>'product_names', 'label'=> $helper->__('Product Name(s)')),
            array('value'=>'product_skus', 'label'=> $helper->__('SKU(s)')),
            array('value'=>'product_options', 'label'=> $helper->__('Product Option(s)')),
            array('value'=>'qnty', 'label'=> $helper->__('Qnty')),
            array('value'=>'weight', 'label'=> $helper->__('Weight')),
            array('value'=>'billing_name', 'label'=> Mage::helper('customer')->__('Bill to Name')),
            array('value'=>'shipping_name', 'label'=> Mage::helper('customer')->__('Shipped to Name')),
            array('value'=>'shipping_method', 'label'=> $helper->__('Shipping Method')),
            array('value'=>'tracking_number', 'label'=> $helper->__('Tracking Number')),
            array('value'=>'shipped', 'label'=> $helper->__('Shipped')),
            array('value'=>'customer_email', 'label'=> $helper->__('Customer Email')),
            array('value'=>'customer_group', 'label'=> $helper->__('Customer Group')),
            array('value'=>'payment_method', 'label'=> $helper->__('Payment Method')),
            array('value'=>'base_tax_amount', 'label'=> $helper->__('Tax Amount (Base)')),
            array('value'=>'tax_amount', 'label'=> $helper->__('Tax Amount (Purchased)')),
            array('value'=>'coupon_code', 'label'=> $helper->__('Coupon Code')),
            array('value'=>'base_discount_amount', 'label'=> $helper->__('Discount (Base)')),
            array('value'=>'discount_amount', 'label'=> $helper->__('Discount (Purchased)')),
            array('value'=>'base_internal_credit', 'label'=> $helper->__('Internal Credit (Base)')), // 20
            array('value'=>'internal_credit', 'label'=> $helper->__('Internal Credit (Purchased)')), // 21
            array('value'=>'billing_company', 'label'=> $helper->__('Bill to Company')),
            array('value'=>'shipping_company', 'label'=> $helper->__('Ship to Company')),
            array('value'=>'billing_city', 'label'=> $helper->__('Bill to City')),
            array('value'=>'shipping_city', 'label'=> $helper->__('Ship to City')),
            array('value'=>'billing_postcode', 'label'=> $helper->__('Billing Postcode')),
            array('value'=>'shipping_postcode', 'label'=> $helper->__('Shipping Postcode')),
            array('value'=>'base_total_refunded', 'label'=> $helper->__('Total Refunded (Base)')),
            array('value'=>'total_refunded', 'label'=> $helper->__('Total Refunded (Purchased)')),
            array('value'=>'grand_total', 'label'=> Mage::helper('customer')->__('Order Total')),
            array('value'=>'order_comment', 'label'=> $helper->__('Order Comment(s)')),
            array('value'=>'order_group', 'label'=> $helper->__('Group')),
            array('value'=>'store_id', 'label'=> $helper->__('Bought From')),
            array('value'=>'is_edited', 'label'=> $helper->__('Edited')),
            array('value'=>'status', 'label'=> Mage::helper('sales')->__('Status')),
            array('value'=>'action', 'label'=> Mage::helper('customer')->__('Action'))
        );
        
        if (!Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
            unset($options[20]); // Internal Credit (Base)
            unset($options[21]); // Internal Credit (Purchased)
        }

        return $options;
    }

    public function toArray()
    {

        $options = array(
            'increment_id',
            'created_at',
            'product_names',
            'product_skus',
            'product_options',
            'qnty',
            'weight',
            'billing_name',
            'shipping_name',
            'shipping_method',
            'tracking_number',
            'shipped',
            'customer_email',
            'customer_group',
            'payment_method',
            'base_tax_amount',
            'tax_amount',
            'coupon_code',
            'base_discount_amount',
            'discount_amount',
            'base_internal_credit',
            'internal_credit',
            'billing_company',
            'shipping_company',
            'billing_city',
            'shipping_city',
            'billing_postcode',
            'shipping_postcode',
            'base_total_refunded',
            'total_refunded',
            'grand_total',
            'order_comment',
            'order_group',
            'store_id',
            'is_edited',
            'status',
            'action'
        );

        if (!Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
            unset($options[20]); // Internal Credit (Base)
            unset($options[21]); // Internal Credit (Purchased)
        }

        return $options;
    }
}