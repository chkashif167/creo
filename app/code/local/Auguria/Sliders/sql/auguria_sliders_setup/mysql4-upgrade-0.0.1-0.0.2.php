<?php
/**
 * Insert slides exemple
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
$installer = $this;
$installer->startSetup();

// Get all CMS pages
$pages = array();
$collection = Mage::getResourceModel('cms/page_collection')->addFieldToSelect('page_id');
if ($collection && $collection->count()>0) {
	foreach ($collection as $item) {
		$pages[]=$item->getPageId();
	}
}

// Get all categories of level 2
$categories = array();
$collection = Mage::getResourceModel('catalog/category_collection')
->addAttributeToSelect('entity_id')
->addAttributeToFilter('level',2);
if ($collection && $collection->count()>0) {
	foreach ($collection as $item) {
		$categories[]=$item->getEntityId();
	}
}

$datas[] = array(
			"name"=>"Abstract",
			"image"=>"auguria/sliders/abstract.jpg",
			"link"=>"http://www.auguria.net/",
			"cms_content"=>"<p>This is an abstract picture.</p>",
			"sort_order"=>1,
			"is_active"=>1,
			"stores"=>array(0),
			"pages"=>$pages,
			"category_ids"=>$categories
		);
$datas[] = array(
			"name"=>"Food",
			"image"=>"auguria/sliders/food.jpg",
			"link"=>"http://www.auguria.net/",
			"cms_content"=>"This is a food picture.",
			"sort_order"=>2,
			"is_active"=>1,
			"stores"=>array(0),
			"pages"=>$pages,
			"category_ids"=>$categories
		);

foreach ($datas as $data) {
	$model = Mage::getModel('auguria_sliders/sliders');
	$model->setData($data);
	$model->save();
}

$installer->endSetup();
