<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Pdp
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Pdp_Model_Shapes extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pdp/shapes');
    }
    public function getImageCollectionByCategory ($category, $keyword = "")
	{
		if ($category === "0") {
			$images = Mage::getModel('pdp/shapes')->getCollection()
			->setOrder('position', 'DESC')
			->setOrder('id', 'DESC');
		} else if($keyword != "") {
            $searchByName = array('like'=>'%'. $keyword .'%');
            $searchByTag = array('like'=>'%'. $keyword .'%');
			$images = Mage::getModel('pdp/shapes')->getCollection()
            //->addFieldToFilter('original_filename', array($searchByName))
			->setOrder('position', 'DESC')
			->setOrder('id', 'DESC');
            $images->getSelect()->where("original_filename like '%". $keyword ."%' OR tag like '%". $keyword ."%'");
        } else {
           /* $category_fillter = array('like'=>'%'. $category .'%');
			$images = Mage::getModel('pdp/images')->getCollection()
			->addFieldToFilter('image_type', 'custom')
			->addFieldToFilter('category', array($category_fillter))
			->setOrder('image_id', 'DESC')
			->setOrder('image_type'); */
            
			$images = Mage::getModel('pdp/shapes')->getCollection()
			->addFieldToFilter('category', $category)
			->setOrder('position', 'DESC')
			->setOrder('id', 'DESC');
        }
		
		return $images;
	}
}