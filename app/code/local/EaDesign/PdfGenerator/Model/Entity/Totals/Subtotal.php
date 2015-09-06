<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Subtotal
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Entity_Totals_Subtotal extends EaDesign_PdfGenerator_Model_Entity_Pdfgenerator
{

    /**
     * Get the variables and values acording to tax rules
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $store = $this->getSource()->getOrder()->getStore();
        $helper = Mage::helper('tax');
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        if ($this->getSource()->getSubtotalInclTax()) {
            $amountInclTax = $this->getSource()->getSubtotalInclTax();
        } else {
            $amountInclTax = $this->getAmount()
                + $this->getSource()->getTaxAmount()
                - $this->getSource()->getShippingTaxAmount();
        }

        $amountInclTax = $this->getOrder()->formatPriceTxt($amountInclTax);


        $totals = array(array(
            'subtotalexcludingtax' => array(
                'value' => $this->getAmountPrefix() . $amount,
                'label' => Mage::helper('tax')->__('Subtotal (Excl. Tax)') . ':',
            ),
            'subtotalincludingtax' => array(
                'value' => $this->getAmountPrefix() . $amountInclTax,
                'label' => Mage::helper('tax')->__('Subtotal (Incl. Tax)') . ':',
            ),
        ));


        return $totals;
    }

    public function getAmount()
    {
        return $this->getSource()->getDataUsingMethod('subtotal');
    }

}

?>
