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
class EaDesign_PdfGenerator_Model_Entity_Totals_Grandtotal extends Mage_Core_Model_Abstract
{

    /**
     * Check if tax amount should be included to grandtotals block
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'variable'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $store = $this->getSource()->getOrder()->getStore();
        $config = Mage::getSingleton('tax/config');

        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $amountExclTax = $this->getAmount() - $this->getSource()->getTaxAmount();
        $amountExclTax = ($amountExclTax > 0) ? $amountExclTax : 0;
        $amountExclTax = $this->getOrder()->formatPriceTxt($amountExclTax);
        $tax = $this->getOrder()->formatPriceTxt($this->getSource()->getTaxAmount());
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        $totals = array(array(
            'grandtotalexcludingtax' => array(
                'value' => $this->getAmountPrefix() . $amountExclTax,
                'label' => Mage::helper('tax')->__('Grand Total (Excl. Tax)') . ':',
            )));
        $totals[] = array(
            'grandtotaltax' => array(
                'value' => $this->getAmountPrefix() . $tax,
                'label' => Mage::helper('tax')->__('Tax') . ':',
            ));
        $totals[] = array(
            'grandtotalincludingtax' => array(
                'value' => $this->getAmountPrefix() . $amount,
                'label' => Mage::helper('tax')->__('Grand Total (Incl. Tax)') . ':',
            ));
        return $totals;
    }


    public function getAmount()
    {
        return $this->getSource()->getDataUsingMethod('grand_total');
    }

}
