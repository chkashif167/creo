<?php

class Magebuzz_Featuredcategory_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLE_FEATURED_CATEGORY     = 'featuredcategory/general/enabled_featuredcategory';
    const XML_PATH_IMAGE_CATEGORY_WIDTH         = 'featuredcategory/display_setting/image_width';
    const XML_PATH_IMAGE_CATEGORY_HEIGHT        = 'featuredcategory/display_setting/image_height';
    const XML_PATH_SHOW_CATEGORIES_ALPHABET     = 'featuredcategory/display_setting/show_category_alphabet';
    const XML_PATH_CATEGORIES_PER_ROW           = 'featuredcategory/display_setting/number_categories_per_row';

    public function isEnabledFeaturedCategories(){
        return (int)Mage::getStoreConfig(self::XML_PATH_ENABLE_FEATURED_CATEGORY);
    }

    public function getImageCategoriesWidth(){
        return (int)Mage::getStoreConfig(self::XML_PATH_IMAGE_CATEGORY_WIDTH);
    }

    public function getImageCategoriesHeight(){
        return (int)Mage::getStoreConfig(self::XML_PATH_IMAGE_CATEGORY_HEIGHT);
    }

    public function isShowCategoriesAlphabet(){
        return (int)Mage::getStoreConfig(self::XML_PATH_SHOW_CATEGORIES_ALPHABET);
    }

    public function getNumberCategoriesPerRow(){
        return (int)Mage::getStoreConfig(self::XML_PATH_CATEGORIES_PER_ROW);
    }

    public function resizeImgCategories($fileName, $basePath, $width, $height)
    {

        $folderURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $imageURL = $folderURL . $fileName;
        $newPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . "magebuzz/featuredcategories/resized/". $width . "x" . $height ."/" . $fileName;
        //if width empty then return original size image's URL
        if ($width != '') {
            //if image has already resized then just return URL
            if (file_exists($basePath) && is_file($basePath) && !file_exists($newPath)) {
                $imageObj = new Varien_Image($basePath);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(FALSE);
                $imageObj->keepFrame(FALSE);
                $imageObj->resize($width, $height);
                $imageObj->save($newPath);
            }

            $resizedURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "magebuzz/featuredcategories/resized/". $width . "x" . $height ."/". $fileName;
            //Zend_Debug::dump($resizedURL);die();
        } else {
            $resizedURL = $imageURL;
        }
        return $resizedURL;
    }

}