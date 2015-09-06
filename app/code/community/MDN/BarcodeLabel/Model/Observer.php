<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_BarcodeLabel_Model_Observer extends Mage_Core_Model_Abstract {

    /**
     * Method called each time an object is saved (and changed :)
     *
     */
    public function model_save_after(Varien_Event_Observer $observer) {

        //check if enabled
        if (!Mage::getStoreConfig('barcodelabel/general/enable'))
            return false;

        $object = $observer->getEvent()->getObject();

        $objectType = mage::helper('BarcodeLabel')->getObjectType($object);

        // detect if the module saved is a product
        if ($objectType == "catalog/product") {

            if ($object->getId() != $object->getOrigData('entity_id')) {

                // check if barcode is already done, if not error message are showing
                $barcodeAttributeName = Mage::helper('BarcodeLabel')->getBarcodeAttribute();

                if (strlen($barcodeAttributeName) == 0)
                    throw new Exception('Barcode attribute not set, create an attribute an save it : in system > configuration > barcodelabel');

                $barcode = $object->getData($barcodeAttributeName);

                if (empty($barcode)) {
                    // generate barcode
                    Mage::helper('BarcodeLabel/Generation')->storeBarcode($object->getId());
                }
            } // end new
            else {
                // check if barcode is already done, if not error message are showing
                $barcodeAttributeName = Mage::helper('BarcodeLabel')->getBarcodeAttribute();

                if (strlen($barcodeAttributeName) == 0)
                    throw new Exception('Barcode attribute not set, create an attribute an save it : in system > configuration > barcodelabel');

                $barcode = $object->getData($barcodeAttributeName);

                if (empty($barcode)) {
                    // generate barcode
                    Mage::helper('BarcodeLabel/Generation')->storeBarcode($object->getId());
                }
            }
        }
    }

    /**
     * Add mass action in catalog > product view
     * @param type $observer
     */
    public function addMassAction($observer) {
        $block = $observer->getEvent()->getBlock();
        if (get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction'
                && $block->getRequest()->getControllerName() == 'catalog_product') {

            $block->addItem('BarcodeLabel', array(
                'label' => Mage::helper('BarcodeLabel')->__('Print barcode labels'),
                'url' => Mage::app()->getStore()->getUrl('BarcodeLabel/Admin/printSelectedProductLabel'),
            ));
        }
    }

    /**
     * ******************************************************************************************************************************* 
     */
}