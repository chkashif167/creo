<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */


class Amasty_Checkoutfees_Model_Options_Allshippingmethods
{
    public function toOptionArray($isActiveOnlyFlag = false)
    {
        $methods  = array(array('value' => '', 'label' => ''));
        $carriers = Mage::getSingleton('shipping/config')->getAllCarriers();
        foreach ($carriers as $carrierCode => $carrierModel) {
            if (!$carrierModel->isActive() && (bool)$isActiveOnlyFlag === true) {
                continue;
            }
            if (get_class($carrierModel) == 'Webshopapps_Matrixrate_Model_Carrier_Matrixrate') {
                $collectionData = Mage::getResourceModel('matrixrate_shipping/carrier_matrixrate_collection')->getData();
                $carrierMethods = array();
                for ($i = 0; $i < count($collectionData); $i++) {
                    $carrierMethods['matrixrate_' . $collectionData[$i]['pk']] = $collectionData[$i]['delivery_type'];
                }
            } else {
                $version = Mage::getVersionInfo();
                if ($carrierCode == 'dhlint' && $version['minor'] < 9) {
                    continue;
                } else {
                    $carrierMethods = $carrierModel->getAllowedMethods();
                }
            }
            if (!$carrierMethods) {
                continue;
            }
            $carrierTitle          = Mage::getStoreConfig('carriers/' . $carrierCode . '/title');
            $methods[$carrierCode] = array(
                'label' => $carrierTitle,
                'value' => array(),
            );
            foreach ($carrierMethods as $methodCode => $methodTitle) {
                $methods[$carrierCode]['value'][] = array(
                    'value' => $carrierCode . '_' . $methodCode,
                    'label' => '[' . $carrierCode . '] ' . $methodTitle,
                );
            }
        }

        return $methods;
    }
}