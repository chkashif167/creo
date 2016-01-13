<?php

class Extensions_Functions_Helper_Data extends Mage_Core_Helper_Abstract
{
	// calculate facebook like loggedin time by providing unix timestamp
	public function updateBreadcrumbs($currentUrl, $label) {
		$pos = strpos($currentUrl, 'universal_categories');		
		if ($pos === false) {
	        return "<strong>$label</strong>";
		} else {
			if($_REQUEST['universal_categories'] > 0){
			$lableLink = substr( $currentUrl, 0, $pos - 1);
			$attribute = Mage::getModel('catalog/resource_eav_attribute')->load(157);
		    $attributeOptions = $attribute->getSource()->getOptionText($_REQUEST['universal_categories']);
            return "<a href='$lableLink' title='$label'>$label</a><span></span></li><li><strong>$attributeOptions</strong>";
			}else{
		        return "<strong>$label</strong>";
			}
		}
	}

}