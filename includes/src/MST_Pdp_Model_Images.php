<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Pdp
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Pdp_Model_Images extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pdp/images');
    }
    public function getArtworkPrice($filename) {
    	$collection = $this->getCollection();
    	$collection->addFieldToFilter("filename", $filename);
    	if ($collection->count()) {
    		return $collection->getFirstItem()->getData('price');
    	} else {
			//Check is this clipart has parent or not.
			$colorImageCollection = Mage::getModel("pdp/colorimage")->getCollection();
			$colorImageCollection->addFieldToFilter("filename", $filename);
			if ($colorImageCollection->count()) {
				$parentImage = $this->load($colorImageCollection->getFirstItem()->getImageId());
				return $parentImage->getPrice();
			}
		}
    	return null;
    }
}