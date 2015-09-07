<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Grid
 *
 * @author eadesignpc
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Block_Sales_Order_Invoice_Grid extends Mage_Adminhtml_Block_Sales_Invoice_Grid
{

    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('invoice_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);


        $templateCollection = Mage::getModel('eadesign/pdfgenerator')->getCollection();
        $templateCollection->addFieldToSelect('*')
            ->addFieldToFilter('pdft_is_active', 1);

        $templates[0] = Mage::helper('sales')->__('Select');
        if($templateCollection->getSize()){
            $templates[999922222] = Mage::helper('sales')->__('Default templates');
        }
        foreach ($templateCollection as $collection) {
            $templates[$collection->getData('pdftemplate_id')] = $collection->getData('pdftemplate_name');
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_orderea', array(
            'label' => Mage::helper('sales')->__('EaDesgin PDF Invoices'),
            'url' => $this->getUrl('adminpdfgenerator/adminhtml_pdfgeneratorpdf/invoicepdfmass', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'template',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Template to print'),
                    'values' => $templates,
                )
            )
        ));

        parent::_prepareMassaction();

        return $this;
    }

}
