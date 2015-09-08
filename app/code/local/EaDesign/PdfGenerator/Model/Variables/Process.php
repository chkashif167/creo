<?php

/**
 * Description of Process
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Variables_Process extends Mage_Core_Model_Variable
{

    public function getVariablesOptionArray($withGroup = false)
    {
        $collection = $this->getCollection();
        $variables = array();
        foreach ($collection->toOptionArray() as $variable) {
            $variables[] = array(
                'value' => '{{customVar code=' . $variable['value'] . '}}',
                'label' => Mage::helper('core')->__('%s', $variable['label'])
            );
        }
        if ($withGroup && $variables) {
            $variables = array(
                'label' => Mage::helper('core')->__('Custom Variables'),
                'value' => $variables
            );
        }

        $variableHelper = Mage::helper('pdfgenerator/variable');

        $helperTotals = $variableHelper->getTotalVariables();
        $helperSubTotals = $variableHelper->getSubtotalVariables();
        $helperItems = $variableHelper->getItemsVariables();
        $helperItemsPrice = $variableHelper->getItemPriceVariables();
        $helperItemsSystem = $variableHelper->getItemSystemVariables();
        $helperDiscount = $variableHelper->getDiscountVariables();
        $helperTax = $variableHelper->getTaxVariables();
        $helperShipping = $variableHelper->getShippingVariables();
        $helperCustomer = $variableHelper->getTheCustomerVariables();
        $helperInfo = $variableHelper->getTheInfoVariables();
        $helperAddress = $variableHelper->getAddressVariables();
        $helperShipPay = $variableHelper->getShipPayVariables();
        $helperInvoice = $variableHelper->getInvoiceVariables();

        $allVars = array(
            $variables,
            $helperTotals,
            $helperSubTotals,
            $helperDiscount,
            $helperTax,
            $helperShipping,
            $helperCustomer,
            $helperInfo,
            $helperAddress,
            $helperShipPay,
            $helperInvoice,
            $helperItemsPrice,
            $helperItemsSystem,
            $helperItems,
        );

        return $allVars;
    }

}