<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Shipping
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Entity_Totals_Shipping extends Mage_Core_Model_Abstract
{

    public function getTotalsForDisplay()
    {
        $store = $this->getOrder()->getStore();
        $config = Mage::getSingleton('tax/config');
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $amountInclTax = $this->getSource()->getShippingInclTax();

        $tax = $this->getOrder()->formatPriceTxt($this->getSource()->getShippingTaxAmount());
        $amountInclTax = $this->getOrder()->formatPriceTxt($amountInclTax);

        $totals = array(array(
            'shipping_amount' => array(
                'value' => $this->getAmountPrefix() . $amount,
                'label' => Mage::helper('tax')->__('Shipping (Excl. Tax)') . ':',
            ),
            'shipping_amountincltax' => array(
                'value' => $this->getAmountPrefix() . $amountInclTax,
                'label' => Mage::helper('tax')->__('Shipping (Incl. Tax)') . ':',
            ),
            'shipping_tax' => array(
                'value' => $this->getAmountPrefix() . $tax,
                'label' => Mage::helper('pdfgenerator')->__('Shipping Tax)') . ':',
            ),
        ));

        return $totals;
    }

    public function getAmount()
    {
        return $this->getSource()->getDataUsingMethod('shipping_amount');
    }

}

?>
