<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Discount
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Entity_Totals_Discount extends Mage_Core_Model_Abstract
{

    /**
     * Get the variables and values acording to tax rules
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $amount = $this->getAmount();
        $amount = $this->getOrder()->formatPriceTxt($amount);

        $totals = array(array(
            'discountammount' => array(
                'value' => $this->getAmountPrefix() . $amount,
                'label' => Mage::helper('tax')->__('Subtotal (Excl. Tax)') . ':',
            )));

        return $totals;
    }

    public function getAmount()
    {
        return $this->getSource()->getDataUsingMethod('discount_amount');
    }

}

?>
