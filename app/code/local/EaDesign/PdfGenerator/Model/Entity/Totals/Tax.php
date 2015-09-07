<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tax
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Entity_Totals_Tax extends Mage_Core_Model_Abstract
{

    /**
     *
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());


        $totalsm = array(array(
            'all_tax_ammount' => array(
                'value' => $this->getAmountPrefix() . $amount,
                'label' => Mage::helper('pdfgenerator')->__('Tax') . ':',
            ),
        ));
        $totalsf = $this->getFullTaxInfo();
        $totals = array_merge($totalsm, $totalsf);

        return $totals;
    }

    public function getFullTaxInfo()
    {
        $taxClassAmount = Mage::helper('tax')->getCalculatedTaxes($this->getOrder());

        if (!empty($taxClassAmount)) {
            $shippingTax = Mage::helper('tax')->getShippingTax($this->getOrder());
            $taxClassAmount = array_merge($shippingTax, $taxClassAmount);


            $i = 0;
            $len = count($taxClassAmount);
            foreach ($taxClassAmount as $tax) {

                $tableHtmlPseudo = '';


                $taxTitle = Mage::Helper('core/string')->cleanString($tax['title']);
                $taxTitle = 'tax_' . preg_replace("[^A-Za-z0-9]", "", $taxTitle);
                $taxTitle = strtolower($taxTitle);
                $percent = $tax['percent'] ? ' (' . floatval($tax['percent']) . '%)' : '';

                $tableHtmlPseudo .= '<tr>';

                $tableHtmlPseudo .= '<td><span>' . $tax['title'] . '     ' . $percent . '</span></td>';
                $tableHtmlPseudo .= '<td><span>' . $this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($tax['tax_amount']) . '</span></td>';

                $tableHtmlPseudo .= '</tr>';

                if ($i++ == 0) {
                    $tableHtml = '<table class="table-rates-tax"><tbody>';
                }

                $tableHtml .= $tableHtmlPseudo;

                if ($i == $len) {
                    $tableHtml .= '</tbody></table>';
                }

                $tax_info[] = array(
                    $taxTitle => array(
                        'value' => $this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($tax['tax_amount']),
                        'label' => Mage::helper('tax')->__($tax['title']) . $percent . ':'
                    ),
                    'pseudotaxtable_data' => array(
                        'value' => $tableHtmlPseudo,
                        'label' => Mage::helper('tax')->__($rate['title']) . $percent . ':',
                    ),
                    'taxtable_data' => array(
                        'value' => $tableHtml,
                        'label' => Mage::helper('tax')->__($rate['title']) . $percent . ':',
                    ));

                $taxClassAmount = $tax_info;
            }

        } else {
            $rates = Mage::getResourceModel('sales/order_tax_collection')->loadByOrder($this->getOrder())->toArray();
            $fullInfo = Mage::getSingleton('tax/calculation')->reproduceProcess($rates['items']);
            $tax_info = array();

            if ($fullInfo) {
                foreach ($fullInfo as $info) {
                    if (isset($info['hidden']) && $info['hidden']) {
                        continue;
                    }

                    $_amount = $info['amount'];

                    foreach ($info['rates'] as $rate) {
                        $percent = $rate['percent'] ? ' (' . $rate['percent'] . '%)' : '';

                        $taxTitle = Mage::Helper('core/string')->cleanString($rate['title']);
                        $taxTitle = 'tax_' . preg_replace("[^A-Za-z0-9]", "", $taxTitle);
                        $taxTitle = strtolower($taxTitle);

                        $tax_info[] = array(
                            $taxTitle => array(
                                'value' => $this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($_amount),
                                'label' => Mage::helper('tax')->__($rate['title']) . $percent . ':',
                            )
                        );
                    }
                }
            }

            $taxClassAmount = $tax_info;
        }

        return $taxClassAmount;
    }

    /**
     * Check if we can display total information in PDF
     *
     * @return bool
     */
    public function canDisplay()
    {
        $amount = $this->getAmount();
        return $this->getDisplayZero() || ($amount != 0);
    }

    public function getAmount()
    {
        return $this->getSource()->getDataUsingMethod('tax_amount');
    }

}
