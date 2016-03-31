<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_MstCore_Model_Image extends Mage_Core_Model_Abstract
{
    protected $_width;
    protected $_height;
    protected $_quality = 90;

    protected $_keepAspectRatio = true;
    protected $_keepFrame = true;
    protected $_keepTransparency = true;
    protected $_constrainOnly = true;
    protected $_backgroundColor = array(255, 255, 255);

    protected $_baseFile;
    protected $_isBaseFilePlaceholder;
    protected $_newFile;
    protected $_processor;
    protected $_destinationSubdir;
    protected $_angle;

    protected $_watermarkFile;
    protected $_watermarkPosition;
    protected $_watermarkWidth;
    protected $_watermarkHeigth;
    protected $_watermarkImageOpacity = 70;

    public function setWidth($width)
    {
        $this->_width = $width;
        return $this;
    }

    public function getWidth()
    {
        return $this->_width;
    }

    public function setHeight($height)
    {
        $this->_height = $height;
        return $this;
    }

    public function getHeight()
    {
        return $this->_height;
    }

    public function setQuality($quality)
    {
        $this->_quality = $quality;
        return $this;
    }

    public function getQuality()
    {
        return $this->_quality;
    }

    public function setKeepAspectRatio($keep)
    {
        $this->_keepAspectRatio = (bool) $keep;
        return $this;
    }

    public function setKeepFrame($keep)
    {
        $this->_keepFrame = (bool) $keep;
        return $this;
    }

    public function setKeepTransparency($keep)
    {
        $this->_keepTransparency = (bool) $keep;
        return $this;
    }

    public function setConstrainOnly($flag)
    {
        $this->_constrainOnly = (bool) $flag;
        return $this;
    }

    public function setBackgroundColor(array $rgbArray)
    {
        $this->_backgroundColor = $rgbArray;
        return $this;
    }

    public function setSize($size)
    {
        // determine width and height from string
        list ($width, $height) = explode('x', strtolower($size), 2);
        foreach (array('width', 'height') as $wh) {
            $$wh = (int) $$wh;
            if (empty($$wh))
                $$wh = null;
        }

        // set sizes
        $this->setWidth($width)->setHeight($height);

        return $this;
    }

    protected function _checkMemory($file = null)
    {
        return $this->_getMemoryLimit() > ($this->_getMemoryUsage() + $this->_getNeedMemoryForFile($file)) || $this->_getMemoryLimit() == - 1;
    }

    protected function _getMemoryLimit()
    {
        $memoryLimit = trim(strtoupper(ini_get('memory_limit')));

        if (! isSet($memoryLimit[0])) {
            $memoryLimit = "128M";
        }

        if (substr($memoryLimit, - 1) == 'K') {
            return substr($memoryLimit, 0, - 1) * 1024;
        }
        if (substr($memoryLimit, - 1) == 'M') {
            return substr($memoryLimit, 0, - 1) * 1024 * 1024;
        }
        if (substr($memoryLimit, - 1) == 'G') {
            return substr($memoryLimit, 0, - 1) * 1024 * 1024 * 1024;
        }
        return $memoryLimit;
    }

    protected function _getMemoryUsage()
    {
        if (function_exists('memory_get_usage')) {
            return memory_get_usage();
        }
        return 0;
    }

    protected function _getNeedMemoryForFile($file = null)
    {
        $file = is_null($file) ? $this->getBaseFile() : $file;
        if (! $file) {
            return 0;
        }

        if (! file_exists($file) || ! is_file($file)) {
            return 0;
        }
        try {
            $imageInfo = getimagesize($file);
        } catch (Exception $e) {
            return 0;
        }

        if (! isset($imageInfo[0]) || ! isset($imageInfo[1])) {
            return 0;
        }
        if (! isset($imageInfo['channels'])) {
            // if there is no info about this parameter lets set it for maximum
            $imageInfo['channels'] = 4;
        }
        if (! isset($imageInfo['bits'])) {
            // if there is no info about this parameter lets set it for maximum
            $imageInfo['bits'] = 8;
        }
        return round(($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + Pow(2, 16)) * 1.65);
    }

    /**
     * Convert array of 3 items (decimal r, g, b) to string of their hex values
     *
     * @param array $rgbArray
     * @return string
     */
    protected function _rgbToString($rgbArray)
    {
        $result = array();
        foreach ($rgbArray as $value) {
            if (null === $value) {
                $result[] = 'null';
            } else {
                $result[] = sprintf('%02s', dechex($value));
            }
        }
        return implode($result);
    }

    /**
     * Set filenames for base file and new file
     *
     * @param string $file
     * @return Mst_Catalog_Model_Product_Image
     */
    public function setBaseFile($file)
    {
        $this->_isBaseFilePlaceholder = false;

        if (($file) && (0 !== strpos($file, '/', 0))) {
            $file = '/' . $file;
        }
        if (($file) && $this->getSubdir()) {
            $file = '/'.$this->getSubdir().$file;
        }

        $baseDir = Mage::getSingleton('mstcore/media_config')->getBaseMediaPath();

        if ('/no_selection' == $file) {
            $file = null;
        }

        if ($file) {

            if ((!file_exists($baseDir . $file)) || ! $this->_checkMemory($baseDir . $file)) {
                $file = null;
            }
        }

        if (!$file) {
            $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
            // check if placeholder defined in config
            $isConfigPlaceholder = Mage::getStoreConfig("catalog/placeholder/{$this->getDestinationSubdir()}_placeholder");
            $configPlaceholder   = '/placeholder/' . $isConfigPlaceholder;

            if ($isConfigPlaceholder && $this->_fileExists($baseDir . $configPlaceholder)) {
                $file = $configPlaceholder;
            }
            else {
                // replace file with skin or default skin placeholder
                $skinBaseDir     = Mage::getDesign()->getSkinBaseDir();
                $skinPlaceholder = "/images/catalog/product/placeholder/{$this->getDestinationSubdir()}.jpg";
                $file = $skinPlaceholder;
                if (file_exists($skinBaseDir . $file)) {
                    $baseDir = $skinBaseDir;
                }
                else {
                    $baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default'));
                    if (!file_exists($baseDir . $file)) {
                        $baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default', '_package' => 'base'));
                    }
                }
            }
            $this->_isBaseFilePlaceholder = true;
        }

        $baseFile = $baseDir . $file;

        if ((! $file) || (! file_exists($baseFile))) {
            return $this;
        }

        $this->_baseFile = $baseFile;

        // build new filename (most important params)
        $path = array(Mage::getSingleton('mstcore/media_config')->getBaseMediaPath(), 'cache', 'images', Mage::app()->getStore()->getId());
        if ($this->getDestinationSubdir()) {
            $path[] = $this->getDestinationSubdir();
        }

        if ((! empty($this->_width)) || (! empty($this->_height)))
            $path[] = "{$this->_width}x{$this->_height}";

        // add misk params as a hash
        $miscParams = array(($this->_keepAspectRatio ? '' : 'non') . 'proportional', ($this->_keepFrame ? '' : 'no') . 'frame',
        ($this->_keepTransparency ? '' : 'no') . 'transparency', ($this->_constrainOnly ? 'do' : 'not') . 'constrainonly', $this->_rgbToString($this->_backgroundColor),
        'angle' . $this->_angle, 'quality' . $this->_quality);

        // if has watermark add watermark params to hash
        if ($this->getWatermarkFile()) {
            $miscParams[] = $this->getWatermarkFile();
            $miscParams[] = $this->getWatermarkImageOpacity();
            $miscParams[] = $this->getWatermarkPosition();
            $miscParams[] = $this->getWatermarkWidth();
            $miscParams[] = $this->getWatermarkHeigth();
        }

        $path[] = md5(implode('_', $miscParams));
        $path_info = pathinfo($file);
        $path[] = trim($path_info['dirname'].md5($file).'.'.$path_info['extension'], '/');
        // append prepared filename
        $this->_newFile = implode('/', $path); // the $file contains heading slash
        return $this;
    }

    public function getBaseFile()
    {
        return $this->_baseFile;
    }

    public function getNewFile()
    {
        return $this->_newFile;
    }

    /**
     * @return Mst_Catalog_Model_Product_Image
     */
    public function setImageProcessor($processor)
    {
        $this->_processor = $processor;
        return $this;
    }

    /**
     * @return Varien_Image
     */
    public function getImageProcessor()
    {
        if (! $this->_processor) {
            $this->_processor = new Varien_Image($this->getBaseFile());
        }
        $this->_processor->keepAspectRatio($this->_keepAspectRatio);
        $this->_processor->keepFrame($this->_keepFrame);
        $this->_processor->keepTransparency($this->_keepTransparency);
        $this->_processor->constrainOnly($this->_constrainOnly);
        $this->_processor->backgroundColor($this->_backgroundColor);
        $this->_processor->quality($this->_quality);
        return $this->_processor;
    }

    /**
     * @see Varien_Image_Adapter_Abstract
     * @return Mst_Catalog_Model_Product_Image
     */
    public function resize()
    {
        if (is_null($this->getWidth()) && is_null($this->getHeight())) {
            return $this;
        }

        # if height not specified, we calcualate it manually
        if ($this->_height == null) {
            $ratio = $this->_width / $this->getImageProcessor()->getOriginalWidth();
            if ($ratio > 1) {
                $this->_height = $this->getImageProcessor()->getOriginalHeight();
            } else {
                $this->_height = $this->getImageProcessor()->getOriginalHeight() * $ratio;
            }
        }


        $this->getImageProcessor()->resize($this->_width, $this->_height);
        return $this;
    }

    /**
     * @see Varien_Image_Adapter_Abstract
     * @return Mirasvit_Content_Model_Article_Image
     */
    public function crop ()
    {
        if (is_null($this->getWidth()) && is_null($this->getHeight())) {
            return $this;
        }
        $this->_keepAspectRatio = true;
        $this->_keepFrame = false;
        $w = $nw = $this->getImageProcessor()->getOriginalWidth();
        $h = $nh = $this->getImageProcessor()->getOriginalHeight();

        $ratio = $w / $h;
        $cropRatio = $this->_width / $this->_height;

        if ($this->getAutoRotate() && (
                ($ratio > 1 && $cropRatio < 1) || ($ratio < 1 && $cropRatio > 1))) {
            $tw = $this->_width;
            $this->setWidth($this->getWidth());
            $this->setHeight($tw);
        }

        $left = $top = 0;
        $right = 0;
        $bottom = 0;

        $scaleW = $scaleH = 1;

        $scaleW = $this->_width / $nw;

        $scaleH = $this->_height / $nh;

        $scale = max($scaleW, $scaleH);
        $nw = $w * $scale;
        $nh = $h * $scale;
        if ($nw > $this->_width) {
            $left = $right = ($nw - $this->_width) / 2;
        }
        if ($nh > $this->_height) {
            $top = $bottom = ($nh - $this->_height) / 2;
        }
        $left = $right = intval($left);
        $top = $bottom = intval($top);

        $this->getImageProcessor()->resize($nw, $nh);
        $this->getImageProcessor()->crop($top, $left, $right, $bottom);
        return $this;
    }

    /**
     * @return Mst_Catalog_Model_Product_Image
     */
    public function rotate($angle)
    {
        $angle = intval($angle);
        $this->getImageProcessor()->rotate($angle);
        return $this;
    }

    /**
     * Set angle for rotating
     *
     * This func actually affects only the cache filename.
     *
     * @param int $angle
     * @return Mst_Catalog_Model_Product_Image
     */
    public function setAngle($angle)
    {
        $this->_angle = $angle;
        return $this;
    }

    /**
     * Add watermark to image
     * size param in format 100x200
     *
     * @param string $fileName
     * @param string $position
     * @param string $size
     * @param int $width
     * @param int $heigth
     * @param int $imageOpacity
     * @return Mst_Catalog_Model_Product_Image
     */
    public function setWatermark($file, $position = null, $size = null, $width = null, $heigth = null, $imageOpacity = null)
    {
        if ($this->_isBaseFilePlaceholder) {
            return $this;
        }

        if ($file) {
            $this->setWatermarkFile($file);
        } else {
            return $this;
        }

        if ($position)
            $this->setWatermarkPosition($position);
        if ($size)
            $this->setWatermarkSize($size);
        if ($width)
            $this->setWatermarkWidth($width);
        if ($heigth)
            $this->setWatermarkHeigth($heigth);
        if ($imageOpacity)
            $this->setImageOpacity($imageOpacity);

        $filePath = $this->_getWatermarkFilePath();

        if ($filePath) {
            $this->getImageProcessor()
                ->setWatermarkPosition($this->getWatermarkPosition())
                ->setWatermarkImageOpacity($this->getWatermarkImageOpacity())
                ->setWatermarkWidth($this->getWatermarkWidth())
                ->setWatermarkHeigth($this->getWatermarkHeigth())
                ->watermark($filePath);
        }

        return $this;
    }

    /**
     * @return Mst_Catalog_Model_Product_Image
     */
    public function saveFile()
    {
        $this->getImageProcessor()->save($this->getNewFile());
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $baseDir = Mage::getSingleton('mstcore/media_config')->getBaseMediaPath();
        $path = str_replace($baseDir, "", $this->_newFile);
        $path = ltrim($path, '/');
        $path = ltrim($path, '\\'); //for windows server
        return Mage::getSingleton('mstcore/media_config')->getBaseMediaUrl() . str_replace(DS, '/', $path);
    }

    public function push()
    {
        $this->getImageProcessor()->display();
    }

    /**
     * @return Mst_Catalog_Model_Product_Image
     */
    public function setDestinationSubdir($dir)
    {
        $this->_destinationSubdir = $dir;
        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationSubdir()
    {
        return $this->_destinationSubdir;
    }

    public function isCached()
    {
        return file_exists($this->_newFile);
    }

    /**
     * Set watermark file name
     *
     * @param string $file
     * @return Mst_Catalog_Model_Product_Image
     */
    public function setWatermarkFile($file)
    {
        $this->_watermarkFile = $file;
        return $this;
    }

    /**
     * Get watermark file name
     *
     * @return string
     */
    public function getWatermarkFile()
    {
        return $this->_watermarkFile;
    }

    /**
     * Get relative watermark file path
     * or false if file not found
     *
     * @return string | bool
     */
    protected function _getWatermarkFilePath()
    {
        $filePath = false;

        if (! $file = $this->getWatermarkFile()) {
            return $filePath;
        }

        $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();

        if( $this->_fileExists($baseDir . '/watermark/stores/' . Mage::app()->getStore()->getId() . $file) ) {
            $filePath = $baseDir . '/watermark/stores/' . Mage::app()->getStore()->getId() . $file;
        } elseif ( $this->_fileExists($baseDir . '/watermark/websites/' . Mage::app()->getWebsite()->getId() . $file) ) {
            $filePath = $baseDir . '/watermark/websites/' . Mage::app()->getWebsite()->getId() . $file;
        } elseif ( $this->_fileExists($baseDir . '/watermark/default/' . $file) ) {
            $filePath = $baseDir . '/watermark/default/' . $file;
        } elseif ( $this->_fileExists($baseDir . '/watermark/' . $file) ) {
            $filePath = $baseDir . '/watermark/' . $file;
        } else {
            $baseDir = Mage::getDesign()->getSkinBaseDir();
            if( $this->_fileExists($baseDir . $file) ) {
                $filePath = $baseDir . $file;
            }
        }
        return $filePath;
    }

    /**
     * Set watermark position
     *
     * @param string $position
     * @return Mst_Catalog_Model_Product_Image
     */
    public function setWatermarkPosition($position)
    {
        $this->_watermarkPosition = $position;
        return $this;
    }

    /**
     * Get watermark position
     *
     * @return string
     */
    public function getWatermarkPosition()
    {
        return $this->_watermarkPosition;
    }

    /**
     * Set watermark image opacity
     *
     * @param int $imageOpacity
     * @return Mst_Catalog_Model_Product_Image
     */
    public function setWatermarkImageOpacity($imageOpacity)
    {
        $this->_watermarkImageOpacity = $imageOpacity;
        return $this;
    }

    /**
     * Get watermark image opacity
     *
     * @return int
     */
    public function getWatermarkImageOpacity()
    {
        return $this->_watermarkImageOpacity;
    }

    /**
     * Set watermark size
     *
     * @param array $size
     * @return Mst_Catalog_Model_Product_Image
     */
    public function setWatermarkSize($size)
    {
        if (is_array($size)) {
            $this->setWatermarkWidth($size['width'])->setWatermarkHeigth($size['heigth']);
        }
        return $this;
    }

    /**
     * Set watermark width
     *
     * @param int $width
     * @return Mst_Catalog_Model_Product_Image
     */
    public function setWatermarkWidth($width)
    {
        $this->_watermarkWidth = $width;
        return $this;
    }

    /**
     * Get watermark width
     *
     * @return int
     */
    public function getWatermarkWidth()
    {
        return $this->_watermarkWidth;
    }

    /**
     * Set watermark heigth
     *
     * @param int $heigth
     * @return Mst_Catalog_Model_Product_Image
     */
    public function setWatermarkHeigth($heigth)
    {
        $this->_watermarkHeigth = $heigth;
        return $this;
    }

    /**
     * Get watermark heigth
     *
     * @return string
     */
    public function getWatermarkHeigth()
    {
        return $this->_watermarkHeigth;
    }

    public function clearCache()
    {
        $directory = Mage::getSingleton('mstcore/media_config')->getBaseMediaPath() . DS . 'content' . DS . 'cache' . DS;
        $io = new Varien_Io_File();
        $io->rmdir($directory, true);
    }

    public function getIsFilePlaceholder()
    {
        return $this->_isBaseFilePlaceholder;
    }

    protected function _fileExists($filename)
    {
        if (file_exists($filename)) {
            return true;
        } else {
            return Mage::helper('core/file_storage_database')->saveFileToFilesystem($filename);
        }
    }
}
