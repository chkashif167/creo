<?php

class MDN_BarcodeLabel_Helper_Label extends Mage_Core_Helper_Abstract {

    // apply coefficient to increase the resolution
    private $_coef = 2;
    private $_customCount = 3;

    /**
     * Return label image for product
     * @param <type> $productId
     */
    public function getImage($productId) {
        
        $product = Mage::getModel('catalog/product')->load($productId);

        //create base image
        $labelSize = Mage::helper('BarcodeLabel')->getlabelSize();
        $height = $labelSize['height'] * $this->_coef;
        $width = $labelSize['width'] * $this->_coef;
        $im = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($im, 255, 255, 255);
        imagefilledrectangle($im, 0, 0, $width, $height, $white);

        //add barcode
        $this->addBarcode($im, $product);

        //add product name
        $this->addName($im, $product);

        //add product attributes
        $this->addProductAttributes($im, $product);

        //add manufacturer
        $this->addManufacturer($im, $product);

        //add logo
        $this->addLogo($im);

        // add product image
        $this->addProductPicture($im, $product);

        //add product sku
        $this->addSku($im, $product);

        //add price
        $this->addPrice($im, $product);

        //add price
        $this->addCustoms($im, $product);

        //return image
        return $im;
    }

    /**
     * Add barcode to img
     * @param <type> $im
     */
    protected function addBarcode(&$image, $product) {

        if (Mage::getStoreConfig('barcodelabel/barcode/print') != 1)
            return false;

        //get barcode image
        $barcodeAttributeName = Mage::helper('BarcodeLabel')->getBarcodeAttribute();
        
        // remove white space before and after the barcode to keep it safe
        $barcode = trim($product->getData($barcodeAttributeName));
        $barcodeImage = Mage::helper('BarcodeLabel/BarcodePicture')->getImage($barcode);

        $barcodeImageWidth = imagesx($barcodeImage);
        $barcodeImageHeight = imagesy($barcodeImage);

        $position = $this->getPositions(Mage::getStoreConfig('barcodelabel/barcode/position'), true);
        
        // get height parameter from extra config input field
        $position['height'] = trim(Mage::getStoreConfig('barcodelabel/barcode/height'));
        $position['height'] = $this->convertCmToPoint($position['height']) ;
        
        // width will always been automatically sized
        $position['width'] = $barcodeImageWidth * $this->_coef;
        
        // manage height dfor barcode
        if ($position['height'] == 0)
            $position['height'] = $barcodeImageHeight * $this->_coef;

        //add barcode on the picture
        imagecopyresized($image, $barcodeImage, $position['x'], $position['y'], 0, 0, $position['width'], $position['height'], $barcodeImageWidth, $barcodeImageHeight);
    }

    /**
     *
     * @param <type> $im
     * @param <type> $product 
     */
    protected function addName(&$im, $product) {

        if (Mage::getStoreConfig('barcodelabel/name/print') != 1)
            return false;

        $name = $product->getName();
        $fontSize = Mage::getStoreConfig('barcodelabel/name/font_size') * $this->_coef;
        $position = $this->getPositions(Mage::getStoreConfig('barcodelabel/name/position'), true);
        $black = $this->setFontColor($im);
        $font = $this->getFont();

        $this->truncateToSize($im, $fontSize, $position, $black, $font, $name);
    }

    /**
     *
     * @param <type> $im
     * @param <type> $product 
     */
    protected function addProductAttributes(&$im, $product) {
        
    }

    /**
     *
     * @param <type> $im
     * @param <type> $product 
     */
    protected function addManufacturer(&$im, $product) {

        if (Mage::getStoreConfig('barcodelabel/manufacturer/print') != 1) {
            return false;
        }
        // get manufaturer attribute
        $attributeCode = trim(Mage::getStoreConfig('barcodelabel/manufacturer/attribute'));
        
        if($attributeCode){
            //$name = $product->getData($attributCode); // NOK return 102
            // get label displayed for frontend
            $name = $product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($product);
        } else {
            return false;
        }
        
        $fontSize = Mage::getStoreConfig('barcodelabel/manufacturer/font_size') * $this->_coef;
        $position = $this->getPositions(Mage::getStoreConfig('barcodelabel/manufacturer/position'), true);
        $black = $this->setFontColor($im);
        $font = $this->getFont();

        $this->truncateToSize($im, $fontSize, $position, $black, $font, $name);
    }

    /**
     *
     * @param <type> $im
     * @param <type> $product 
     */
    protected function addLogo(&$im) {

        if (Mage::getStoreConfig('barcodelabel/logo/print') != 1)
            return false;

        // get uploaded path in /media/upload/image/my.png
        $logoPath = Mage::getBaseDir() . DS . 'media' . DS . 'upload' . DS . 'image' . DS . Mage::getStoreConfig('barcodelabel/logo/picture');
        
        // get extension file
        $extention = strtolower( pathinfo($logoPath, PATHINFO_EXTENSION) );

        // detect if the file are jpeg or png 
        switch ($extention) {
            case 'jpeg':
            case 'jpg':
                $logoImg = imagecreatefromjpeg($logoPath);
                break;
            
            case 'png':
                $logoImg = imagecreatefrompng($logoPath);
                break;
            
            default:
                $logoImg = NULL;
                break;
        }
      
        if( !empty($logoImg) ){
            $position = $this->getPositions(Mage::getStoreConfig('barcodelabel/logo/position'), true);

            $logoImageWidth = imagesx($logoImg);
            $logoImageHeight = imagesy($logoImg);

            imagecopyresized($im, $logoImg, $position['x'], $position['y'], 0, 0, $position['width'], $position['height'], $logoImageWidth, $logoImageHeight);
        }
        
    }

    /**
     * create product image
     * @return boolean 
     */
    protected function addProductPicture(&$im, $product) {
        
        if (Mage::getStoreConfig('barcodelabel/product_image/print') != 1)
            return false;

        // get magento gallery object
        $gallery = $product->getmedia_gallery(); 
        
        // check if image exists (first image only)
        if (isset($gallery["images"]['0'])) {

            // get path of base image | "no_selection"
            if ($product->getimage() != "no_selection")
                $logoPath = Mage::getBaseDir() . DS . 'media' . DS . 'catalog' . DS . 'product' . $product->getimage();
            // or small image
            if ($product->getsmall_image() != "no_selection")
                $logoPath = Mage::getBaseDir() . DS . 'media' . DS . 'catalog' . DS . 'product' . $product->getsmall_image();
            // or thumnail who is the best for label
            if ($product->getthumbnail() != "no_selection")
                $logoPath = Mage::getBaseDir() . DS . 'media' . DS . 'catalog' . DS . 'product' . $product->getthumbnail();

            // get extension file
            $extention = strtolower( pathinfo($logoPath, PATHINFO_EXTENSION) );

            // detect if the file are jpeg or png 
            switch ($extention) {
                case 'jpeg';
                case 'jpg';
                    $logoImg = imagecreatefromjpeg($logoPath);
                    break;
                case 'png':
                    $logoImg = imagecreatefrompng($logoPath);
                    break;
                default:
                    return false;
                    break;
            }

            $position = $this->getPositions(Mage::getStoreConfig('barcodelabel/product_image/position'), true);

            $logoImageWidth = imagesx($logoImg);
            $logoImageHeight = imagesy($logoImg);

            imagecopyresized($im, $logoImg, $position['x'], $position['y'], 0, 0, $position['width'], $position['height'], $logoImageWidth, $logoImageHeight);
        } // end if image exist
    }

    /**
     *
     * @param <type> $im
     * @param <type> $product 
     */
    protected function addSku(&$im, $product) {

        if (Mage::getStoreConfig('barcodelabel/sku/print') != 1)
            return false;

        $name = $product->getSku();
        $fontSize = Mage::getStoreConfig('barcodelabel/sku/font_size') * $this->_coef;
        $position = $this->getPositions(Mage::getStoreConfig('barcodelabel/sku/position'), true);
        $black = $this->setFontColor($im);
        $font = $this->getFont();

        $this->truncateToSize($im, $fontSize, $position, $black, $font, $name);
    }

    /**
     *
     * @param <type> $im
     * @param <type> $product 
     */
    protected function addPrice(&$im, $product) {

        if (Mage::getStoreConfig('barcodelabel/price/print') != 1)
            return false;
        
        //get base price
        $price = $product->getPrice();
        
        // get special price with range date depends on configuration flag yes/no
        if ( Mage::getStoreConfig('barcodelabel/price/special_price') && $product->getspecial_price() != '') {
            if (Mage::app()->getLocale()->isStoreDateInInterval(Mage::app()->getStore(), $product->getspecial_from_date(), $product->getspecial_to_date())){
                $price = $product->getspecial_price();
            }
        }

        //tax rate
        $taxRate = (float) Mage::getStoreConfig('barcodelabel/price/tax_rate');
        $price = $price * (1 + $taxRate / 100);

        // to do : choose symbol or code ( € | EUR )
        $currencyCode = Mage::getStoreConfig('barcodelabel/price/currency');
        $currency = Mage::getModel('directory/currency')->load($currencyCode);
        
        // place the symbol at the right of the price value
        $price = $currency->format($price, array(), false, false);

        $fontSize = Mage::getStoreConfig('barcodelabel/price/font_size') * $this->_coef;
        $position = $this->getPositions(Mage::getStoreConfig('barcodelabel/price/position'), true);
        $black = $this->setFontColor($im);
        $font = $this->getFont();

        imagettftext($im, $fontSize, 0, $position['x'], $position['y'], $black, $font, $price);
    }

    /**
     * Add custom zones
     * @param type $im
     * @param type $product 
     */
    protected function addCustoms($im, $product) {
        
        //for each custom zone
        for ($i = 1; $i <= $this->_customCount; $i++) {
            
            //if zone is not enabled
            if (!Mage::getStoreConfig('barcodelabel/custom_' . $i . '/print'))
                continue;
            
            //get input value
            $content = Mage::getStoreConfig('barcodelabel/custom_' . $i . '/content');
            // get the value inside {}
            $regex = "/\{([^\}]*)\}/";
            
            preg_match_all($regex, $content, $matches);
            
            if (isset($matches[1])){
                foreach($matches[1] as $match){ 
                    
                    // get value from the attribute (like "weight")
                    $value = $product->getAttributeText($match);
                    
                    if ($value){
                        $content = str_replace('{'.$match.'}', $value, $content);
                    }
                    
                    // if getAttributeText() failed , try getFrontend()
                    if(!$value) {
                        $content = $product->getResource()->getAttribute($match)->getFrontend()->getValue($product);
                                                
                         // if get data fail try get data
                         if( $content == ""){
                            $content = $product->getData($match);
                        }
                    }
                }
            }
            
            //print
            $fontSize = Mage::getStoreConfig('barcodelabel/custom_' . $i . '/font_size') * $this->_coef;
            $position = $this->getPositions(Mage::getStoreConfig('barcodelabel/custom_' . $i . '/position'), true);
            $black = $this->setFontColor($im);
            $font = $this->getFont();
            imagettftext($im, $fontSize, 0, $position['x'], $position['y'], $black, $font, $content);
        }
    }

    /**
     * Convert cm to point
     * @param <type> $cm
     * @return <type>
     */
    protected function convertCmToPoint($cm) {
        return $cm * 50;
    }

    /**
     *
     * @param type $positionString
     * @param type $convertToPoint
     * @return type 
     */
    public function getPositions($positionString, $convertToPoint) {
        
        // [0]=> X, [1]=> Y, [2]=> Width, [3]=> Height 
        $param = explode(',', $positionString);
        $positions = array('x'=>'0','y'=>'0','width'=>'0','height'=>'0');
        
        // make sure that x and y are getting
        if(count($param) > 0){
            $positions['x'] = $param[0];
            $positions['y'] = $param[1];
        }
        
        // for the barcode configuration the width is missing, so manage it
        if (array_key_exists('2', $param))
            $positions['width'] = $param[2];
        // the same for the height
        if (array_key_exists('3', $param)) 
            $positions['height'] = $param[3];
        
        if ($convertToPoint) {
            foreach ($positions as $k => $value) {
                $positions[$k] = $this->convertCmToPoint($value) * $this->_coef;
            }
        }

        return $positions;
    }

    /**
     * Return font
     * @return string
     */
    protected function getFont() {
        $path = Mage::getBaseDir() . '/media/font/' . Mage::getStoreConfig('barcodelabel/label/font');
        return $path;
    }
    
    /**
     * Wrap text if too large
     * @param <type> $text
     * @param <type> $width
     * @param <type> $font
     * @param <type> $size
     */
    protected function truncateToSize($image, $fontSize, $position, $color, $font, $text) {
        
        $imageWidth = imagesx($image);
        $words = explode(' ', $text);
        $lines = array($words[0]);
        $currentLine = 0;
        
        for($i=1; $i<count($words); $i++){
            
            $lineSize = imagettfbbox($fontSize, NULL, $font, $lines[$currentLine] . ' ' . $words[$i]);
            
            // if the text width in pixel is lower than the main image width
            if($lineSize[2] - $lineSize[0] < $imageWidth){
                // then add the word into the current string
                $lines[$currentLine] .= ' ' . $words[$i];
            }else{
                // else, jump to the next line
               $currentLine++;
               $lines[$currentLine] = $words[$i];
            }
        }
                
        // Loop through the lines and place them on the image
        $lineCount = 1;
        foreach ($lines as $line){
            
            // get size for each lines
            $lineBox = imagettfbbox($fontSize, NULL, $font, "$line");
            
            // for the first line, get the position 'y' from config
            if($lineCount == 1){
                $lineHeight = $position['y'];
            } else {
                $lineHeight =  $lineBox[1] - $lineBox[7];
            }
            
            $linePositionForX = $position['x'];
            $linePositionForY = $position['y'] + $fontSize * $lineCount; // to add extraline => (($lineHeight + $fontSize ) * $lineCount)
            
            // draw the wrapped line as image in the main image
            imagettftext($image, $fontSize, 0, $linePositionForX, $linePositionForY, $color, $font, $line);

            $lineCount++;
        }

    }
    
    /**
     * add a color into the image for the writted characters
     * 
     * @param type $im
     * @param type $colorTab
     * @return type 
     */
    public function setFontColor($im, $colorTab = array(0,0,0)){
        
        // default is black : 0,0,0
        return imagecolorallocate($im, $colorTab[0], $colorTab[1], $colorTab[2]); 
    }

}
