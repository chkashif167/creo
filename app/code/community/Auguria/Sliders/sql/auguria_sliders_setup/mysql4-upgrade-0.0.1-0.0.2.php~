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
			"name"=>"Ara bleu",
			"image"=>"auguria/sliders/Ara.jpg",
			"link"=>"http://www.auguria.net/",
			"cms_content"=>"<p>L'<a title='Ara bleu' href='http://fr.wikipedia.org/wiki/Ara_bleu'>Ara bleu</a> est un perroquet pr&eacute;sent en <a title='Am&eacute;rique latine' href='http://fr.wikipedia.org/wiki/Am%C3%A9rique_latine'>Am&eacute;rique latine</a>.</p>",
			"sort_order"=>1,
			"is_active"=>1,
			"stores"=>array(0),
			"pages"=>$pages,
			"category_ids"=>$categories
		);
$datas[] = array(
			"name"=>"Anax empereur",
			"image"=>"auguria/sliders/Anax.jpg",
			"link"=>"http://www.auguria.net/",
			"cms_content"=>"<p>Un <a style='color: #e26703;' title='Anax empereur' href='http://fr.wikipedia.org/wiki/Anax_empereur'>Anax empereur</a> (<em>Anax imperator</em>). Photo prise &agrave; Gubbeen, dans le comt&eacute; de Cork (Irlande).</p>",
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