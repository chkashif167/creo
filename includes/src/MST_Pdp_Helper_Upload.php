<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Pdp
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
require_once(Mage::getBaseDir("lib") . DS . "WideImage" . DS . "WideImage.php");
class MST_Pdp_Helper_Upload extends Mage_Core_Helper_Abstract {
    public $uploadDir;
    public $uploadMediaUrl;
    public function __construct() {
        $this->uploadDir = Mage::getBaseDir("media") . DS . "pdp" . DS . "images" . DS . "upload" . DS ;
        $this->uploadMediaUrl = Mage::getBaseUrl("media") . "/pdp/images/upload/";
    }
    public function isImagickLoaded() {
        return extension_loaded('imagick');   
    }
    public function isGetImageSizeLoaded() {
        return function_exists('getimagesize');
    }
    //$size_str look like this: 200M, 200k, or 200g
    private function returnBytes ($size_str)
    {
        switch (substr ($size_str, -1))
        {
            case 'M': case 'm': return (float)$size_str * 1048576;
            case 'K': case 'k': return (float)$size_str * 1024;
            case 'G': case 'g': return (float)$size_str * 1073741824;
            default: return $size_str;
        }
    }
    //Return upload_max_filesize in byte, KB or M
    public function getUploadMaxFileSize($type) {
        $maxFileSize = ini_get("upload_max_filesize");
        $sizeInByte = (float) $this->returnBytes($maxFileSize);
        if($type) {
            switch ($type) {
                case 'K': case 'k' : return $sizeInByte / 1024;
                case 'M': case 'M' : return $sizeInByte / 1048576;
                default : return $sizeInByte;
            }
        }
    }
    /**
    return true if image is real
    return false otherwise
    **/
    public function isRealImage($imagePath) {
        //Security Considerations by check getimagesize
        if(is_array($this->getImageSize($imagePath))) {
           return true;
        } else {
            //Return true anyway, there is no function to check
            if(!$this->isGetImageSizeLoaded()) {
                return true;
            }
        }
        return false;
    }
    /**
    return array of image size
    return false otherwise
    **/
    public function getImageSize($imagePath) {
        if($this->isGetImageSizeLoaded()) {
            try {
                $result = list($width, $height, $type, $attr) = getimagesize($imagePath);
                if(is_array($result)) {
                    return $result;
                } 
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }
    /**
    @params $cropData ($width, $height, $top, $left)
    return array crop status
    **/
    public function cropImage($imagePath, $cropData) {
        $response = array();
        if(!$this->isRealImage($imagePath)) {
            $response['status'] = 'error';
            $response['message'] = 'Image file not valid. Please check again!';
            return $response;
        }
        if($this->isImagickLoaded()) {
            $cropResult = $this->cropImageUseImagick($imagePath, $cropData);
        } else {
            //GD crop might be
            $cropResult = $this->cropImageUseWideImage($imagePath, $cropData);
        }
        if (!$cropResult) {
            $response['status'] = 'error';
            $response['message'] = 'Can not crop this image! Please check again!';
            return $response;
        } else {
            $response['status'] = 'success';
            $response['message'] = 'Image have been cropped successfully!';
            $response['crop_image'] = $cropResult;
            return $response;
        }
    }
    /**
    @required Imagick
    return cropped image path if cropped success, FALSE otherwise
    **/
    private function cropImageUseImagick($imagePath, $cropData) {
        //Get filename
        $temp = explode(DS, $imagePath);
        $filename = end($temp);
        $outFile = "crop-img-" . time() . '_' . $filename;
        $newPath = $this->uploadDir . $outFile;
        try {
            $image = new Imagick($imagePath);
            $image->cropImage($cropData['w'], $cropData['h'], $cropData['x'], $cropData['y']);
            $result = $image->writeImage($newPath);
            if ($result) {
                return $this->uploadMediaUrl . $outFile;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    /**
    @required GD
    return cropped image path if cropped success, FALSE otherwise
    **/
    private function cropImageUseWideImage($imagePath, $cropData) {
        //Get filename
        $temp = explode(DS, $imagePath);
        $filename = end($temp);
        $outFile = "crop-wide-" . time() . '_' . $filename;
        $newPath = $this->uploadDir . $outFile;
        try {
            $image = WideImage::load($imagePath);
            $newImage = $image->crop($cropData['x'], $cropData['y'], $cropData['w'], $cropData['h']);
            //Image quality, if jpg then set quality to 100
            $fileExt = explode(".", $outFile);
            if (end($fileExt) == "jpg" || end($fileExt) == "jpeg") {
                $newImage->saveToFile($newPath, 100);
            } else {
                $newImage->saveToFile($newPath);
            }
            if(file_exists($newPath)) {
                return $this->uploadMediaUrl . $outFile;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    //Return size config
    public function getConfig($outputType = "") {
        $config = array();
        //All size in byte unit
        $config['size_unit'] = Mage::getStoreConfig("pdp/custom_upload/size_unit");
        $uploadMaxSize = (float) Mage::getStoreConfig("pdp/custom_upload/upload_max_size");
        $uploadMinSize = (float) Mage::getStoreConfig("pdp/custom_upload/upload_min_size");
        $config['upload_max_size'] = $this->returnBytes($uploadMaxSize . $config['size_unit']);
        $config['upload_min_size'] = $this->returnBytes($uploadMinSize . $config['size_unit']);
        $config['upload_max_filesize'] = $this->getUploadMaxFileSize('b');
        $config['max_size_alert'] = $this->__("This file is too big. The maximum upload size is: " . $uploadMaxSize . $config['size_unit']);
        $config['min_size_alert'] = $this->__("This file is too small. Please upload image file equal or bigger than : " . $uploadMinSize . $config['size_unit']);
        if ($outputType === "JSON") {
            return json_encode($config);
        }
        return $config;
    }
    public function getUploadNote() {
        return Mage::getStoreConfig("pdp/custom_upload/upload_note");
    }
    public function addWatermark($source) {
        $watermarkConfig = Mage::helper("pdp")->getWatermarkConfig();
        if($watermarkConfig['active'] != "1") return false;
        try {
            $image = WideImage::load($source);
            $watermarkImgPath = $watermarkConfig['watermark_path'];
            $watermark = WideImage::load($watermarkImgPath);
            $position = explode("_", $watermarkConfig['position']);
            if(empty($position) || count($position) != 2) {
                $position[0] = "bottom";
                $position[1] = "right";
            }
            $new = $image->merge($watermark, $position[0], $position[1], 100);
            //Overwrite the source file
            $result = $new->saveToFile($source);
            return true;
        } catch (Exception $e) {
            
        }
        return false;
    }
}
