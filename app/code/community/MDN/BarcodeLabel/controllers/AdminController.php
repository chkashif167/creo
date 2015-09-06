<?php

class MDN_BarcodeLabel_AdminController extends Mage_Adminhtml_Controller_Action {

    /**
     * Generate barcodes for all products
     * except barcode already existing 
     */
    public function GenerateForAllProductsAction() {
        
        try {
            Mage::helper('BarcodeLabel/Generation')->generateForAllProducts();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Barcodes generated'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        }

        //confirm & redirect
        $this->_redirect('adminhtml/system_config/edit', array('section' => 'barcodelabel'));
    }

    /**
     * Return label preview
     */
    public function LabelPreviewAction() {
        //get datas
        $productId = $this->getRequest()->getParam('product_id');
        $img = Mage::helper('BarcodeLabel/Label')->getImage($productId);
        
        //return image
        header('Content-type: image/gif');
        imagegif($img);
        die();
    }

    /**
     * Print labels for 1 product
     */
    public function PrintProductLabelsAction() {

        //Get data
        $productId = $this->getRequest()->getParam('product_id');
        $count = $this->getRequest()->getParam('count');
        $param = array($productId => $count);

        //create PDF
        $pdfModel = Mage::getModel('BarcodeLabel/Pdf_Labels');
        $pdf = $pdfModel->getPdf($param);
        $this->_prepareDownloadResponse(mage::helper('BarcodeLabel')->__('Barcode labels') . '.pdf', $pdf->render(), 'application/pdf');
    }

    /**
     * Print labels for all children products of a configurable product
     */
    public function PrintChildrenProductLabelsAction() {

        // array that contain id of children with stock  
        $arrayStockChildren = array();

        // get the id of config product
        $productId = $this->getRequest()->getParam('product_id');

        // get the ids of childrens products
        $childrenIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($productId);

        foreach ($childrenIds[0] as $keyChildrenId => $valueChildrenId) {

            // get the stock of each children products
            $qtyStock = (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($valueChildrenId)->getQty();

            //do not allow to print more than 100 labels per product
            if ($qtyStock > 100)
                $qtyStock = 100;

            // formatting an array
            $arrayStockChildren[$valueChildrenId] = $qtyStock;
        }


        //create PDF
        $pdfModel = Mage::getModel('BarcodeLabel/Pdf_Labels');
        $pdf = $pdfModel->getPdf($arrayStockChildren);
        $this->_prepareDownloadResponse(mage::helper('BarcodeLabel')->__('Barcode labels') . '.pdf', $pdf->render(), 'application/pdf');
    }

    /**
     * mass action on product for printing labels 
     */
    public function printSelectedProductLabelAction() {

        // getting id of each product selected
        $productIds = $this->getRequest()->getPost('product');

        // array that contain id of children with stock  
        $arrayStockChildren = array();

        foreach ($productIds as $productId) {

            $productType = Mage::getModel('catalog/product')->load($productId)->getTypeId();
            
            // for simple or bundle
            if ($productType != Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $qtyStock = (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId)->getQty();
                $arrayStockChildren[$productId] = $qtyStock;
            } else {
                // if selected product is a parent id, then find childrens id
                $childrenIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($productId);
                foreach ($childrenIds[0] as $valueChildrenId) {
                    $qtyStock = (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($valueChildrenId)->getQty();
                    $arrayStockChildren[$valueChildrenId] = $qtyStock;
                }
            }

        }

        //create PDF
        $pdfModel = Mage::getModel('BarcodeLabel/Pdf_Labels');
        $pdf = $pdfModel->getPdf($arrayStockChildren);
        $this->_prepareDownloadResponse(mage::helper('BarcodeLabel')->__('Barcode labels') . '.pdf', $pdf->render(), 'application/pdf');
    }

    /**
     * Manage barcode list grid
     */
    public function ManageListAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     *
     */
    public function uploadListAction()
    {
        try {
            //save text file
            $uploader = new Varien_File_Uploader('file');
            $uploader->setAllowedExtensions(array('csv', 'txt'));
            $path = Mage::app()->getConfig()->getTempVarDir() . '/';
            $uploader->save($path);

            if ($uploadFile = $uploader->getUploadedFileName()) {
                $filePath = $path . $uploadFile;
                $method = $this->getRequest()->getPost('mode');
                $count = Mage::helper('BarcodeLabel/List')->import($filePath, $method);
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('File imported (%s codes added)', $count));
            }
            else
                throw new Exception('Unable to get file name');
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        }

        $this->_redirect('*/*/ManageList');
    }

    /**
     * Export barcodes
     */
    public function ExportAction()
    {
        $fileContent = Mage::helper('BarcodeLabel/Export')->getContent();
        $this->_prepareDownloadResponse('barcode_product_list.csv', $fileContent);
    }
    
}