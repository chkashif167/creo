<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog data helper
 */
class EaDesign_PdfGenerator_Helper_Tax extends Mage_Tax_Helper_Data
{

    /**
     * Get calculated taxes for each tax class
     *
     * This method returns array with format:
     * array(
     *  $index => array(
     *      'tax_amount'        => $taxAmount,
     *      'base_tax_amount'   => $baseTaxAmount,
     *      'hidden_tax_amount' => $hiddenTaxAmount
     *      'title'             => $title
     *      'percent'           => $percent
     *  )
     * )
     *
     * @param Mage_Sales_Model_Order $source
     * @return array
     */
    public function getCalculatedTaxes($source)
    {
        if (Mage::registry('current_invoice')) {
            $current = Mage::registry('current_invoice');
        } elseif (Mage::registry('current_creditmemo')) {
            $current = Mage::registry('current_creditmemo');
        } else {
            $current = $source;
        }

        $taxClassAmount = array();
        if ($current && $source) {
            foreach ($current->getItemsCollection() as $item) {
                $taxCollection = Mage::getResourceModel('tax/sales_order_tax_item')
                    ->getTaxItemsByItemId(
                        $item->getOrderItemId() ? $item->getOrderItemId() : $item->getItemId()
                    );

                foreach ($taxCollection as $tax) {
                    $taxClassId = $tax['tax_id'];
                    $percent = $tax['tax_percent'];

                    $price = $item->getRowTotal();
                    $basePrice = $item->getBaseRowTotal();
                    if ($this->applyTaxAfterDiscount($item->getStoreId())) {
                        $price = $price - $item->getDiscountAmount() + $item->getHiddenTaxAmount();
                        $basePrice = $basePrice - $item->getBaseDiscountAmount() + $item->getBaseHiddenTaxAmount();
                    }

                    if (isset($taxClassAmount[$taxClassId])) {
                        $taxClassAmount[$taxClassId]['tax_amount'] += $price * $percent / 100;
                        $taxClassAmount[$taxClassId]['base_tax_amount'] += $basePrice * $percent / 100;
                    } else {
                        $taxClassAmount[$taxClassId]['tax_amount'] = $price * $percent / 100;
                        $taxClassAmount[$taxClassId]['base_tax_amount'] = $basePrice * $percent / 100;
                        $taxClassAmount[$taxClassId]['title'] = $tax['title'];
                        $taxClassAmount[$taxClassId]['percent'] = $tax['percent'];
                    }
                }
            }

            foreach ($taxClassAmount as $key => $tax) {
                if ($tax['tax_amount'] == 0 && $tax['base_tax_amount'] == 0) {
                    unset($taxClassAmount[$key]);
                }
            }

            $taxClassAmount = array_values($taxClassAmount);
        }

        return $taxClassAmount;
    }

    /**
     * Get calculated Shipping & Handling Tax
     *
     * This method returns array with format:
     * array(
     *  $index => array(
     *      'tax_amount'        => $taxAmount,
     *      'base_tax_amount'   => $baseTaxAmount,
     *      'hidden_tax_amount' => $hiddenTaxAmount
     *      'title'             => $title
     *      'percent'           => $percent
     *  )
     * )
     *
     * @param Mage_Sales_Model_Order $source
     * @return array
     */
    public function getShippingTax($source)
    {
        if (Mage::registry('current_invoice')) {
            $current = Mage::registry('current_invoice');
        } elseif (Mage::registry('current_creditmemo')) {
            $current = Mage::registry('current_creditmemo');
        } else {
            $current = $source;
        }

        $taxClassAmount = array();
        if ($current && $source) {
            if ($current->getShippingTaxAmount() != 0 && $current->getBaseShippingTaxAmount() != 0) {
                $taxClassAmount[0]['tax_amount'] = $current->getShippingTaxAmount();
                $taxClassAmount[0]['base_tax_amount'] = $current->getBaseShippingTaxAmount();
                if ($current->getShippingHiddenTaxAmount() > 0) {
                    $taxClassAmount[0]['hidden_tax_amount'] = $current->getShippingHiddenTaxAmount();
                }
                $taxClassAmount[0]['title'] = $this->__('Shipping & Handling Tax');
                $taxClassAmount[0]['percent'] = NULL;
            }
        }

        return $taxClassAmount;
    }

}
