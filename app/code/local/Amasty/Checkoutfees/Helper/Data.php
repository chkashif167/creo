<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function renderFeeOptions($fee, $options)
    {
        $html = '';
        if (isset($options) && is_array($options) && $fee->getInput() > 0) {
            $template = Mage::app()->getLayout()->createBlock('core/template');
            $template->setData('options', $options);
            switch ($fee->getInput()) {
                // select
                case 1:
                    $template->setTemplate('amasty/amcheckoutfees/render/select.phtml');
                    $template->setData('feeId', $fee->getFeesId());
                    $html = $template->toHtml();
                    break;

                // checkbox
                case 2:
                    $template->setTemplate('amasty/amcheckoutfees/render/checkbox.phtml');
                    $html = $template->toHtml();
                    break;

                // radio
                case 3:
                    $template->setTemplate('amasty/amcheckoutfees/render/radio.phtml');
                    $html = $template->toHtml();
                    break;

                // none
                default :
                    $html = '';
                    break;
            }
        }

        return $html;
    }

    /*
     * get list of customer groups for adminhtml settings
     */
    public function getCustomerGroups()
    {
        $customerGroup = array();

        $customer_group = new Mage_Customer_Model_Group();
        $allGroups      = $customer_group->getCollection()->toOptionHash();
        foreach ($allGroups as $key => $allGroup) {
            $customerGroup[$key] = array('value' => $key, 'label' => $allGroup);
        }

        return $customerGroup;
    }

    public function getFees($type)
    {
        // get saved into quote data with Checkoutfees options
        $fees      = array();
        $quote     = Mage::getSingleton('checkout/session')->getQuote();
        $savedFees = $quote->getAmcheckoutfeesFees() ? unserialize($quote->getAmcheckoutfeesFees()) : array();

        // get all fees for payment
        $allFees = Mage::getModel('amcheckoutfees/fees')
                       ->getCollection()
                       ->addFieldToFilter('enabled', 1)
            ->addFieldToFilter('autoapply', 0)
                       ->setOrder('sort', 'ASC');


        // filter cart, payment and shipping
        if ($type == 'cart') {
            $allFees->addFieldToFilter('position_cart', 1);
        } else if ($type == 'payment') {
            $allFees->addFieldToFilter('position_checkout', 2);
        } else if ($type == 'shipping') {
            $allFees->addFieldToFilter('position_checkout', 1);
        }

        // remove all fees that does not match rule for current Quote
        $allFees->validateAllFees();

        // process fees collections
        if ($allFees->getSize()) {
            foreach ($allFees as $fee) {
                // get all options for fees
                $feeOptions = Mage::getModel('amcheckoutfees/feesData')
                                  ->getCollection()
                                  ->addFieldToFilter('fees_id', array('eq' => $fee->getFeesId()))
                                  ->setOrder('sort', 'ASC')
                                  ->getItems();

                // replace default values with saved
                foreach ($feeOptions as &$feeOption) {
                    if (empty($savedFees[$fee->getFeesId()])) {
                        $feeOption->setChecked($feeOption->getIsDefault());
                    } else if (in_array($feeOption->getFeesDataId(), explode(',', $savedFees[$fee->getFeesId()]))) {
                        $feeOption->setChecked(1);
                    }
                    $feeOption->setIsDefault(0);
                }
                // save all fee data for render
                $fees[$fee->getFeesId()] = array(
                    'fee'     => $fee,
                    'options' => $feeOptions
                );
            }
        }

        return $fees;
    }

}