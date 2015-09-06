<?php

class MDN_BarcodeLabel_Model_Pdf_Labels extends Mage_Sales_Model_Order_Pdf_Abstract {

    public $pdf;
    protected $_labelWidth = 0;
    protected $_labelHeight = 0;
    protected $_labelNumber = 0; // label number per lines
    protected $_globalLabelNumber = 0; // label number per qty
    protected $_totalLabelNumber = 0; // total label number
    protected $_topMargin = 0;
    protected $_leftMargin = 0;
    protected $_rightMargin = 0;
    protected $_bottomMargin = 0;
    protected $_verticalSpacing = 0;
    protected $_horizontalSpacing = 0;
    protected $_labelsPerRow = 0;
    protected $_rowCount = 0;
    protected $_defaultLabelHeight = 120;
    protected $_currentPage;
    protected $debug = '';

    /**
     * Main method
     *
     * @param unknown_type $orders
     * @return unknown
     */
    public function getPdf($products = array()) {

        //init environment
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        //set width and height
        $widthCm = mage::getStoreConfig('barcodelabel/pdf/paper_width');
        $heightCm = mage::getStoreConfig('barcodelabel/pdf/paper_height');
        $this->_PAGE_WIDTH = $this->cmToPixels($widthCm);
        $this->_PAGE_HEIGHT = $this->cmToPixels($heightCm);
        $this->calculateWidthAndMargin($products);

        //init pdf
        if ($this->pdf == null) {
            $this->pdf = new Zend_Pdf();
        } else {
            $this->firstPageIndex = count($this->pdf->pages);
        }

        //add new page
        $this->_currentPage = $this->NewPage();

        //draw labels
        foreach ($products as $productId => $qty) {
            for($i = 1; $i <= $qty; $i++){
                $this->printLabel($productId, $qty);
            }
        }

        return $this->pdf;
    }

   /**
    *
    * @param type $productId
    * @param type $qty 
    */
    public function printLabel($productId, $qty) {

        // update laber number
        $this->_globalLabelNumber ++;
        //$this->_labelNumber++;
        
        //draw outline
        $rectX = $this->getLabelX();
        $rectY = $this->getLabelY();

        // draw border if configuration allow it
        if (Mage::getStoreConfig('barcodelabel/label/border')){
            $this->_currentPage->drawRectangle($rectX, $rectY, $rectX + $this->_labelWidth, $rectY - $this->_labelHeight, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
        }
        
        // get image
        $img = Mage::helper('BarcodeLabel/Label')->getImage($productId);

        // convert image ressouce gd to image object for zend image
        $imageObject = $this->pngToZendImage($img);

        $this->_currentPage->drawImage($imageObject, $rectX +1 ,($rectY - $this->_labelHeight) + 4 ,($rectX + $this->_labelWidth) -4, $rectY -4);

        $this->prepareForNextLabel();
    }

    /**
     * Prepare for next label
     *
     */
    protected function prepareForNextLabel() {
        
        // label number per lines
        $this->_labelNumber++;

        //if we exceed page bottom, create new page
        if (mage::getStoreConfig('barcodelabel/pdf/paper_height') > 0) {

            if ( (($this->getLabelY() - $this->_labelHeight) < 0) && ($this->_globalLabelNumber < $this->_totalLabelNumber) ) {
                $this->newPage();
            }
        }
    }

    /**
     * Return current label X
     *
     * @return unknown
     */
    protected function getLabelX() {
        $col = ($this->_labelNumber % $this->_labelsPerRow);
        $x = $this->_leftMargin + ($col * ($this->_labelWidth + $this->_horizontalSpacing));
        return $x;
    }

    /**
     * Return current label Y
     *
     * @return unknown
     */
    protected function getLabelY() {
        $row = (int) ($this->_labelNumber / $this->_labelsPerRow);
        $y = ($this->_PAGE_HEIGHT - $this->_topMargin) - ($row) * ($this->_labelHeight + $this->_verticalSpacing);
        return $y;
    }

    /**
     * Add a new page
     *
     * @param array $settings
     * @return unknown
     */
    public function newPage(array $settings = array()) {
        $page = $this->pdf->newPage($this->_PAGE_WIDTH . ':' . $this->_PAGE_HEIGHT . ':');
        $this->pdf->pages[] = $page;
        $this->_currentPage = $page;
        $this->_labelNumber = 0;

        //retourne la page
        return $this->_currentPage;
    }

    /**
     * Convert png image to zend image
     * @param <type> $pngImage
     * @return <type>
     */
    protected function pngToZendImage($pngImage) {
        //save png image to disk
        $path = Mage::getBaseDir() . DS . 'var' . DS . 'barcode_image.png';
        imagepng($pngImage, $path);

        //create zend picture
        $zendPicture = Zend_Pdf_Image::imageWithPath($path);

        //delete file
        unlink($path);

        //return
        return $zendPicture;
    }

    /**
     * Convert cm to pixels
     *
     * @param unknown_type $value
     * @return unknown
     */
    public function cmToPixels($value) {
        return $value * 28.33;
    }

    /**
     * Calculate labels width & height
     *
     */
    protected function calculateWidthAndMargin($products) {

        $this->_topMargin = $this->cmToPixels(mage::getStoreConfig('barcodelabel/pdf/top_margin'));
        $this->_leftMargin = $this->cmToPixels(mage::getStoreConfig('barcodelabel/pdf/left_margin'));
        $this->_rightMargin = $this->cmToPixels(mage::getStoreConfig('barcodelabel/pdf/right_margin'));
        $this->_bottomMargin = $this->cmToPixels(mage::getStoreConfig('barcodelabel/pdf/bottom_margin'));

        $this->_verticalSpacing = $this->cmToPixels(mage::getStoreConfig('barcodelabel/pdf/vertical_inter_margin'));
        $this->_horizontalSpacing = $this->cmToPixels(mage::getStoreConfig('barcodelabel/pdf/horizontal_inter_margin'));

        $this->_labelsPerRow = mage::getStoreConfig('barcodelabel/pdf/labels_per_row');
        $this->_rowCount = mage::getStoreConfig('barcodelabel/pdf/row_count');

        //Calculate labels count
        $labelCount = 0;
        foreach ($products as $id => $qty){
            $labelCount += $qty;
        }
        $this->_totalLabelNumber = $labelCount;
        
        //if height is not set, calculate final height for all labels
        if ($this->_PAGE_HEIGHT == 0) {
            $this->_rowCount = ceil($labelCount / $this->_labelsPerRow);
            $this->_PAGE_HEIGHT = $this->_rowCount * ($this->_defaultLabelHeight + $this->_topMargin + $this->_bottomMargin + $this->_verticalSpacing);
        }

        $usableWidth = ($this->_PAGE_WIDTH - $this->_leftMargin - $this->_rightMargin);
        $usableHeight = ($this->_PAGE_HEIGHT - $this->_topMargin - $this->_bottomMargin);

        $this->_labelWidth = ($usableWidth - (($this->_labelsPerRow - 1) * $this->_horizontalSpacing)) / $this->_labelsPerRow;

        $this->_labelHeight = ($usableHeight - (($this->_rowCount - 1) * $this->_verticalSpacing)) / $this->_rowCount;


    }

}