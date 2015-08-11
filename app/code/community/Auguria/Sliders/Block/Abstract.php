<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
abstract class Auguria_Sliders_Block_Abstract extends Mage_Core_Block_Template
{
	protected $_pageType;
	
	public function getSlides()
	{
		if (Mage::getStoreConfig('auguria_sliders/general/enabled')) {
			$slides = Mage::getResourceModel('auguria_sliders/sliders_collection')
							->addStoreFilter(Mage::app()->getStore()->getId())
							->addFilter('is_active',true)
	                        ->setOrder('sort_order', 'ASC');
			return $slides;
		}
		return new Varien_Data_Collection();
	}
	
	public function displayLink($slide)
	{
		$link = $slide->getLink();
		return !empty($link);
	}
	
	public function displayImage($slide)
	{
		$imagePath = $this->getMediaPath($slide);
		return is_file($imagePath);
	}
	
	public function displayCmsContent($slide)
	{
		$cmsContent = $slide->getCmsContent();
		return !empty($cmsContent);
	}
	
	public function getMediaPath($slide, $width=null, $height=null)
	{
		$baseName = basename($slide->getImage());
		if ($width==null && $height==null) {
			return Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'auguria' . DS . 'sliders' . DS . $baseName;
		}
		return Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'auguria' . DS . 'sliders' . DS . 'resized' . DS . $width.'x'.$height . DS . $baseName;
	}
	
	public function getMediaUrl($slide, $width=null, $height=null)
	{
		$baseName = basename($slide->getImage());
		if ($width==null && $height==null) {
			return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'auguria/sliders/' . $baseName;
		}
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'auguria/sliders/resized/' . $width.'x'.$height . '/' . $baseName;
	}
	
	public function getResizedImage($slide, $width=null, $height=null)
	{
		// If base image exists
		if (is_file($this->getMediaPath($slide))) {
			// If no resize return base image url
			if ($width==null && $height==null) {
				return $this->getMediaUrl($slide);
			}
			// If resized image doesn't exists : process resize
			elseif (!is_file($this->getMediaPath($slide, $width, $height))) {
	    		$imageObj = new Varien_Image($this->getMediaPath($slide));
	    		$imageObj->constrainOnly(true);
	    		$imageObj->keepAspectRatio(true);
	    		$imageObj->keepFrame(false);
	    		$imageObj->resize($width, $height);
	    		$imageObj->save($this->getMediaPath($slide, $width, $height));
	    		// If resized image exists : return resized url
	    		if (is_file($this->getMediaPath($slide, $width, $height))) {
	    			return $this->getMediaUrl($slide, $width, $height);
	    		}
			}
			// Resized image exists : return it
			else {
				return $this->getMediaUrl($slide, $width, $height);
			}
		}
		return '';
	}
	
	public function setPageType($type)
	{
		$this->_pageType = $type;
	}
	
	public function getPageType()
	{
		if (!isset($this->_pageType)) {
			$template = $this->getLayout()->getBlock('root')->getTemplate();			
			$patterns = array('empty'=>'/empty/','one'=>'/1/','two'=>'/2/','three'=>'/3/');
			foreach ($patterns as $type=>$pattern) {
				if (preg_match($pattern, $template)) {
					$this->_pageType = $type;
					break;
				}
			}
		}
		return $this->_pageType;
	}
	
	public function getCaptionHeight()
	{
		$configPath = 'auguria_sliders/general/'.$this->getPageType().'_caption_height';
		return (int)Mage::getStoreConfig($configPath);
	}
	
	public function getImageWidth()
	{
		$configPath = 'auguria_sliders/general/'.$this->getPageType().'_image_width';
		return (int)Mage::getStoreConfig($configPath);
	}
	
	public function getImageHeight()
	{
		$configPath = 'auguria_sliders/general/'.$this->getPageType().'_image_height';
		return (int)Mage::getStoreConfig($configPath);
	}
}