<?php

class Progos_Creomob_Helper_Data extends Mage_Core_Helper_Abstract {


	

	public function getAttributesFromCategoryId($categoryId){
        $attributeSets = array(
            'ClothingMen' => array(57),
            'ClothingWomen' => array(58),
            'ClothingTshirt' => array(17,59),
            'ClothingPolo' => array(18,60),
            'CapsMesh' => array(55),
            'CapsBacks' => array(56),
            'AccessoriesCases' => array(40),
            'AccessoriesNotebooks' => array(42)
        );
        
        $attributeSet_attributes = array(   
            'ClothingMen' => array('universal_categories','tshirt_color','polo_color','size','styles'),
            'ClothingWomen' => array('universal_categories','tshirt_color','polo_color','size','styles'),
            'ClothingTshirt' => array('universal_categories','size','styles','tshirt_color'),
            'ClothingPolo' => array('universal_categories','size','polo_color','polo_print_color'),
            'CapsMesh' => array('universal_categories','cap_mesh_color'),
            'CapsBacks' => array('universal_categories','cap_snap_color'),
            'AccessoriesCases' => array('universal_categories','acc_phone_model','acc_phone_color',
                'acc_phone_print_type'),
            'AccessoriesNotebooks'=> array('universal_categories','acc_notebook_color','acc_notebook_material')
            
        );
        
        $attributeSet = '';
        $attributes = array();
        foreach($attributeSets as $attrSet=>$categories){
            if(is_array($categories)){
                if(in_array($categoryId, $categories)){
                    $attributeSet = $attrSet;
                    if(key_exists($attributeSet, $attributeSet_attributes)){
                        $attributes = $attributeSet_attributes[$attributeSet];
//                        break;
                    }
                }
            }
        }
        return $attributes;
    }
}