<?php

class Extensions_Functions_Helper_Data extends Mage_Core_Helper_Abstract
{
	// update breadcrumbs w.r.t category
	public function updateBreadcrumbs($currentUrl, $label) {
		$pos = strpos($currentUrl, 'universal_categories');		
		if ($pos === false) {
	        return "<strong>$label</strong>";
		} else {
			if($_REQUEST['universal_categories'] > 0){
				$lableLink = substr( $currentUrl, 0, $pos - 1);				
				$attribute = Mage::getModel('catalog/resource_eav_attribute')->load(157);
				$attributeOptions = $attribute->getSource()->getOptionText($_REQUEST['universal_categories']);
				if($attributeOptions){
					return "<a href='$lableLink' title='$label'>$label</a><span></span></li><li><strong>$attributeOptions</strong>";
				}else{
					return "<strong>$label</strong>";
				}
			}else{
		        return "<strong>$label</strong>";
			}
		}
	}

	// get parent link for collection
	public function getCollectionParentLink($currentUrl) {
		$pos = strpos($currentUrl, 'men');		
		if ($pos === false) {
	        //return "<strong>$label</strong>";
		} else {
			return '<div class="skewbtn1"><a href="'.Mage::getBaseUrl()."collections/index/categories".'" id="goback" title="Back"><span>Back</span></a></div>';
		}
		$pos = strpos($currentUrl, 'women');		
		if ($pos === false) {
	        //return "<strong>$label</strong>";
		} else {
			return '<div class="skewbtn1"><a href="'.Mage::getBaseUrl()."collections/index/categories".'" id="goback" title="Back"><span>Back</span></a></div>';
		}
	}

	// get parent link for listing
	public function getListingParentLink($currentUrl) {
		$breadcrumbs = Mage::app()->getLayout()->getBlock('breadcrumbs');
		$path = Mage::helper('catalog')->getBreadcrumbPath();
		$reverse = end($path);
		if( $reverse['link'] ){
			return '<div class="skewbtn1"><a href="'.$reverse['link'].'" id="goback" title="Back"><span>Back</span></a></div>';
		}else{
			end($path);
			$secondLast = prev($path);
			if( $secondLast['link'] ){ 					
				$pos = strpos($currentUrl, 'universal_categories');		
				if ($pos === false) {
					//return "<strong>$label</strong>";
				} else {
					if($_REQUEST['universal_categories'] > 0){
						$lableLink = substr( $currentUrl, 0, $pos - 1);
						return '<div class="skewbtn1"><a href="'.$lableLink.'" id="goback" title="Back"><span>Back</span></a></div>';					
					}else{
						return '<div class="skewbtn1"><a href="'.$secondLast['link'].'" id="goback" title="Back"><span>Back</span></a></div>';					
					}
				}
				return '<div class="skewbtn1"><a href="'.$secondLast['link'].'" id="goback" title="Back"><span>Back</span></a></div>';					
			}else{
				$pos = strpos($currentUrl, 'universal_categories');		
				if ($pos === false) {
					//return "<strong>$label</strong>";
				} else {
					if($_REQUEST['universal_categories'] > 0){
						if(!($_REQUEST['custom'])){
							$lableLink = substr( $currentUrl, 0, $pos - 1);
							return '<div class="skewbtn1"><a href="'.$lableLink.'" id="goback" title="Back"><span>Back</span></a></div>';												
						}
					}else{
						//return '<div class="skewbtn1"><a href="'.$secondLast['link'].'" id="goback" title="Back">Back</a></div>';					
					}
				}
			}
		}
	}
}