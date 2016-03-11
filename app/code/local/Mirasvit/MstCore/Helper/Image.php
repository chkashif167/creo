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


class Mirasvit_MstCore_Helper_Image extends Mage_Core_Helper_Abstract
{
    protected $_model;
    protected $_scheduleResize = false;
    protected $_scheduleCrop = false;
    protected $_scheduleRotate = false;
    protected $_scheduleAutoRotate = false;
    protected $_angle;

    protected $_watermark;
    protected $_watermarkPosition;
    protected $_watermarkSize;
    protected $_watermarkImageOpacity;

    protected $_item;
    protected $_imageFile;
    protected $_placeholder;

    public function init (Varien_Object $item, $attributeName, $imageFolder = null, $imageFile = null)
    {
        $this->_reset();
        $this->_setModel(Mage::getModel('mstcore/image'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        $this->_getModel()->setSubdir($imageFolder);
        $this->setItem($item);
        $this->addWatermark();

        if ($imageFile) {
            $this->setImageFile($imageFile);
        } else {
            $this->_getModel()->setBaseFile(
                    $this->getItem()
                        ->getData($this->_getModel()
                        ->getDestinationSubdir()));
        }
        return $this;
    }

    public function getPlaceholder ()
    {
        $attr = $this->_getModel()->getDestinationSubdir();
        $subdir = $this->_getModel()->getSubdir();
        if ($subdir) {
            $attr = $subdir . '/' . $attr;
        }

        $this->_placeholder = 'images/placeholder/' . $attr . '.jpg';
        return $this->_placeholder;
    }

    public function isImagePlaceholder ()
    {
        return $this->_getModel()->getIsFilePlaceholder();
    }

    /**
     * Reset all previos data
     */
    protected function _reset ()
    {
        $this->_model = null;
        $this->_scheduleResize = false;
        $this->_scheduleCrop = false;
        $this->_scheduleRotate = false;
        $this->_angle = null;
        $this->_watermark = null;
        $this->_watermarkPosition = null;
        $this->_watermarkSize = null;
        $this->_watermarkImageOpacity = null;
        $this->_item = null;
        $this->_imageFile = null;
        return $this;
    }

    /**
     * Reset all previos data
     */
    public function reset ()
    {
        $this->_scheduleResize = false;
        $this->_scheduleCrop = false;
        $this->_scheduleRotate = false;
        $this->_angle = null;
        $this->_watermark = null;
        $this->_watermarkPosition = null;
        $this->_watermarkSize = null;
        $this->_watermarkImageOpacity = null;

        $oldModel = $this->_getModel();
        $newModel = Mage::getModel('mstcore/image');
        $newModel->setDestinationSubdir($oldModel->getDestinationSubdir())
            ->setSubdir($oldModel->getSubdir());

        $this->_setModel($newModel);

        if ($this->_imageFile) {
            $this->setImageFile($this->_imageFile);
        } else {
            // add for work original size
            $this->_getModel()->setBaseFile(
                    $this->getItem()
                        ->getData($this->_getModel()
                        ->getDestinationSubdir()));
        }
        return $this;
    }

    public function addWaterMark ()
    {
        if ($this->_getModel()->getSubdir() == 'catalog/product') {
            $this->setWatermark(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_image"));
            $this->setWatermarkImageOpacity(
                    Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_imageOpacity"));
            $this->setWatermarkPosition(
                    Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position"));
            $this->setWatermarkSize(
                    Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size"));
        }

    }

    /**
     * Schedule resize of the image
     * $width *or* $height can be null - in this case, lacking dimension will be
     * calculated.
     *
     * @see Mst_Catalog_Model_Product_Image
     * @param $width int
     * @param $height int
     * @return Mst_Catalog_Helper_Image
     */
    public function resize ($width, $height = null)
    {
        $this->_getModel()
            ->setWidth($width)
            ->setHeight($height);
        $this->_scheduleResize = true;
        return $this;
    }

    public function autoRotate ($flag)
    {
        $this->_scheduleAutoRotate = $flag;
        $this->_getModel()->setAutoRotate($flag);
        return $this;
    }

    public function crop ($width, $height)
    {
        $this->_getModel()
            ->setWidth($width)
            ->setHeight($height);
        $this->_scheduleCrop = true;
        return $this;
    }

    /**
     * Set image quality, values in percentage from 0 to 100
     *
     * @param $quality int
     * @return Mst_Catalog_Helper_Image
     */
    public function setQuality ($quality)
    {
        $this->_getModel()->setQuality($quality);
        return $this;
    }

    /**
     * Guarantee, that image picture width/height will not be distorted.
     * Applicable before calling resize()
     * It is true by default.
     *
     * @see Mst_Catalog_Model_Product_Image
     * @param $flag bool
     * @return Mst_Catalog_Helper_Image
     */
    public function keepAspectRatio ($flag)
    {
        $this->_getModel()->setKeepAspectRatio($flag);
        return $this;
    }

    /**
     * Guarantee, that image will have dimensions, set in $width/$height
     * Applicable before calling resize()
     * Not applicable, if keepAspectRatio(false)
     *
     * $position - TODO, not used for now - picture position inside the frame.
     *
     * @see Mst_Catalog_Model_Product_Image
     * @param $flag bool
     * @param $position array
     * @return Mst_Catalog_Helper_Image
     */
    public function keepFrame ($flag, $position = array('center', 'middle'))
    {
        $this->_getModel()->setKeepFrame($flag);
        return $this;
    }

    /**
     * Guarantee, that image will not lose transparency if any.
     * Applicable before calling resize()
     * It is true by default.
     *
     * $alphaOpacity - TODO, not used for now
     *
     * @see Mst_Catalog_Model_Product_Image
     * @param $flag bool
     * @param $alphaOpacity int
     * @return Mst_Catalog_Helper_Image
     */
    public function keepTransparency ($flag, $alphaOpacity = null)
    {
        $this->_getModel()->setKeepTransparency($flag);
        return $this;
    }

    /**
     * Guarantee, that image picture will not be bigger, than it was.
     * Applicable before calling resize()
     * It is false by default
     *
     * @param $flag bool
     * @return Mst_Catalog_Helper_Image
     */
    public function constrainOnly ($flag)
    {
        $this->_getModel()->setConstrainOnly($flag);
        return $this;
    }

    /**
     * Set color to fill image frame with.
     * Applicable before calling resize()
     * The keepTransparency(true) overrides this (if image has transparent
     * color)
     * It is white by default.
     *
     * @see Mst_Catalog_Model_Product_Image
     * @param $colorRGB array
     * @return Mst_Catalog_Helper_Image
     */
    public function backgroundColor ($colorRGB)
    {
        // assume that 3 params were given instead of array
        if (! is_array($colorRGB)) {
            $colorRGB = func_get_args();
        }
        $this->_getModel()->setBackgroundColor($colorRGB);
        return $this;
    }

    public function rotate ($angle)
    {
        $this->setAngle($angle);
        $this->_getModel()->setAngle($angle);
        $this->_scheduleRotate = true;
        return $this;
    }

    /**
     * Add watermark to image
     * size param in format 100x200
     *
     * @param $fileName string
     * @param $position string
     * @param $size string
     * @param $imageOpacity int
     * @return Mst_Catalog_Helper_Image
     */
    public function watermark ($fileName, $position, $size = null, $imageOpacity = null)
    {
        $this->setWatermark($fileName)
            ->setWatermarkPosition($position)
            ->setWatermarkSize($size)
            ->setWatermarkImageOpacity($imageOpacity);
        return $this;
    }

    public function placeholder ($fileName)
    {
        $this->_placeholder = $fileName;
    }

    public function __toString ()
    {
        Varien_Profiler::start(__CLASS__.'::'.__FUNCTION__);
        try {
            if ($this->getImageFile()) {
                $this->_getModel()->setBaseFile($this->getImageFile());
            } else {
                $this->_getModel()->setBaseFile(
                        $this->getItem()
                            ->getData($this->_getModel()
                            ->getDestinationSubdir()));
            }

            if ($this->_getModel()->isCached()) {
                return $this->_getModel()->getUrl();
            } else {
                if ($this->_scheduleRotate) {
                    $this->_getModel()->rotate($this->getAngle());
                }

                if ($this->_scheduleResize) {
                    $this->_getModel()->resize();
                }

                if ($this->_scheduleCrop) {
                    $this->_getModel()->crop();
                }

                if ($this->getWatermark()) {
                    $this->_getModel()->setWatermark($this->getWatermark());
                }

                $url = $this->_getModel()
                    ->saveFile()
                    ->getUrl();
            }
        } catch (Exception $e) {
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        Varien_Profiler::stop(__CLASS__.'::'.__FUNCTION__);
        return $url;
    }

    /**
     * Enter description here...
     *
     * @return Mst_Catalog_Helper_Image
     */
    protected function _setModel ($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mst_Catalog_Model_Product_Image
     */
    protected function _getModel ()
    {
        return $this->_model;
    }

    protected function setAngle ($angle)
    {
        $this->_angle = $angle;
        return $this;
    }

    protected function getAngle ()
    {
        return $this->_angle;
    }

    /**
     * Set watermark file name
     *
     * @param $watermark string
     * @return Mst_Catalog_Helper_Image
     */
    protected function setWatermark ($watermark)
    {
        $this->_watermark = $watermark;
        $this->_getModel()->setWatermarkFile($watermark);
        return $this;
    }

    /**
     * Get watermark file name
     *
     * @return string
     */
    protected function getWatermark ()
    {
        return $this->_watermark;
    }

    /**
     * Set watermark position
     *
     * @param $position string
     * @return Mst_Catalog_Helper_Image
     */
    protected function setWatermarkPosition ($position)
    {
        $this->_watermarkPosition = $position;
        $this->_getModel()->setWatermarkPosition($position);
        return $this;
    }

    /**
     * Get watermark position
     *
     * @return string
     */
    protected function getWatermarkPosition ()
    {
        return $this->_watermarkPosition;
    }

    /**
     * Set watermark size
     * param size in format 100x200
     *
     * @param $size string
     * @return Mst_Catalog_Helper_Image
     */
    public function setWatermarkSize ($size)
    {
        $this->_watermarkSize = $size;
        $this->_getModel()->setWatermarkSize($this->parseSize($size));
        return $this;
    }

    /**
     * Get watermark size
     *
     * @return string
     */
    protected function getWatermarkSize ()
    {
        return $this->_watermarkSize;
    }

    /**
     * Set watermark image opacity
     *
     * @param $imageOpacity int
     * @return Mst_Catalog_Helper_Image
     */
    public function setWatermarkImageOpacity ($imageOpacity)
    {
        $this->_watermarkImageOpacity = $imageOpacity;
        $this->_getModel()->setWatermarkImageOpacity($imageOpacity);
        return $this;
    }

    /**
     * Get watermark image opacity
     *
     * @return int
     */
    protected function getWatermarkImageOpacity ()
    {
        if ($this->_watermarkImageOpacity) {
            return $this->_watermarkImageOpacity;
        }

        return $this->_getModel()->getWatermarkImageOpacity();
    }

    protected function setItem ($item)
    {
        $this->_item = $item;
        return $this;
    }

    protected function getItem ()
    {
        return $this->_item;
    }

    protected function setImageFile ($file)
    {
        $this->_imageFile = $file;
        return $this;
    }

    protected function getImageFile ()
    {
        return $this->_imageFile;
    }

    protected function parseSize ($string)
    {
        $size = explode('x', strtolower($string));
        if (sizeof($size) == 2) {
            return array('width' => ($size[0] > 0) ? $size[0] : null, 'heigth' => ($size[1] > 0) ? $size[1] : null);
        }
        return false;
    }

    /**
     * Retrieve original image width
     *
     * @return int null
     */
    public function getOriginalWidth ()
    {
        return $this->_getModel()
            ->getImageProcessor()
            ->getOriginalWidth();
    }

    /**
     * Retrieve original image height
     *
     * @return int null
     */
    public function getOriginalHeight ()
    {
        return $this->_getModel()
            ->getImageProcessor()
            ->getOriginalHeight();
    }

    /**
     * Retrieve Original image size as array
     * 0 - width, 1 - height
     *
     * @return array
     */
    public function getOriginalSizeArray ()
    {
        return array($this->getOriginalWidth(), $this->getOriginalHeight());
    }

    /**
     * Check - is this file an image
     *
     * @param $filePath string
     * @return bool @throw Mage_Core_Exception
     */
    public function validateUploadFile ($filePath)
    {
        if (! getimagesize($filePath)) {
            Mage::throwException($this->__('Disallowed file type.'));
        }
        return true;
    }
}
