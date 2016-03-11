<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Tagproducts
 * @copyright   Copyright (c) 2014 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_Tagproducts_Model_Source_Tags  {
	
	 /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
    	$tags=Mage::getModel('tag/tag')->getCollection();
    	$tag_list=array();
    	
    	foreach ($tags as $tag) 
    		array_push($tag_list, array('value' => $tag->getId(), 'label'=> $tag->getName()));
    	 
        return  $tag_list;
    }
}