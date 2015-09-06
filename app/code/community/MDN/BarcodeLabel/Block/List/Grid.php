<?php

class MDN_BarcodeLabel_Block_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('BarcodeLabelListGrid');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText('No Items');
    }

    /**
     * 
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {
        $barcodeAttributeId = Mage::helper('BarcodeLabel')->getBarcodeAttributeId();
        $collection = Mage::getModel('BarcodeLabel/List')
                ->getCollection()
                ;
       
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $helper = Mage::helper('BarcodeLabel');

        $this->addColumn('bll_id', array(
            'header'=> $helper->__('Id'),
            'index' => 'bll_id'
        ));

        $this->addColumn('bll_barcode', array(
            'header'=> $helper->__('Barcode'),
            'index' => 'bll_barcode'
        ));

        /*
        $this->addColumn('sku', array(
            'header'=> $helper->__('Sku'),
            'index' => 'sku'
        ));
        */
        
        return parent::_prepareColumns();
    }

    /**
     *
     * @return type 
     */
    public function getGridParentHtml()
    { 
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/uploadList');
    }
    
}

