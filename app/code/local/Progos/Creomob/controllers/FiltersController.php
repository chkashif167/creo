<?php


class Progos_Creomob_FiltersController extends Mage_Core_Controller_Front_Action{
    
    
    
    
    public function categoryFilterTree() {
        $categories = Mage::getModel('catalog/category')
                        ->getCollection()
                        ->addAttributeToSelect('*')
                        ->addIsActiveFilter()
//                        ->addLevelFilter(3)
                        ->addAttributeToFilter('level', array('gt' => 1))
                        ->addAttributeToFilter('is_active', '1')
                        ->addAttributeToFilter('include_in_menu', '1')
                        ->addAttributeToSort('path', 'asc')
                        ->load()->toArray();
        return $categories;
    }
    
    public function categoryFilterTreeAction() {
        $categories = $this->categoryFilterTree();
        $data = array();
        foreach ($categories as $category) {
            $data[] = $category;
        }
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }
    
    public function getAttribute($attributeCode) {
        $attributeId = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setCodeFilter($attributeCode)->getFirstItem()->getAttributeId();
        
        $attributeOptions = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attributeId)
                ->setStoreFilter(0)
                ->setPositionOrder()
                ->load()
                ->toOptionArray();
        
        return $attributeOptions;

        $attrs = array();
        foreach ($attributeOptions AS $attributeOption) {
            $attrs[] = array('code'=>$attributeOption['value'],'label'=>$attributeOption['label']);
        }
        
        header("Content-Type: application/json");
        print_r(json_encode($attrs));
        die;
    }

    public function colorFilterAction() {
        $attributeCode = "color";
        $attributeOptions = $this->getAttribute($attributeCode);
        $attrs = array();
        foreach ($attributeOptions AS $attributeOption) {
            $attrs[] = array('code'=>$attributeOption['value'],'label'=>$attributeOption['label']);
        }
        
        header("Content-Type: application/json");
        print_r(json_encode($attrs));
        die;
    }
    
    public function sizeFilterAction() {
        $attributeCode = "size";
        $attributeOptions = $this->getAttribute($attributeCode);
        $attrs = array();
        foreach ($attributeOptions AS $attributeOption) {
            $attrs[] = array('code'=>$attributeOption['value'],'label'=>$attributeOption['label']);
        }
        
        header("Content-Type: application/json");
        print_r(json_encode($attrs));
        die;
    }
    
    public function filtersAction(){
        $categories = $this->categoryFilterTree();
        $data = array();
        foreach ($categories as $category) {
            $data['categories'][] = $category;
        }
        $attrs = array();
        
        $attributeCode = "color";
        $attributeOptions = $this->getAttribute($attributeCode);
//        $attribute = Mage::getModel('catalog/resource_eav_attribute')
//            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
//        $attributeOptions = $attribute ->getSource()->getAllOptions(false); 
        foreach ($attributeOptions AS $attributeOption) {
            $attrs['color'][] = $attributeOption;
        }
        
        $attributeCode = "size";
//        $attributeOptions = $this->getAttribute($attributeCode);
        $attribute = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        $attributeOptions = $attribute ->getSource()->getAllOptions(false); 
        foreach ($attributeOptions AS $attributeOption) {
            $attrs['size'][] = $attributeOption;
        }
        
        $attributeCode = "gender";
        $attribute = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        $attributeOptions = $attribute ->getSource()->getAllOptions(false); 
        foreach ($attributeOptions AS $attributeOption) {
            $attrs['gender'][] = $attributeOption;
        }
        
        $attributeCode = "styles";
//        $attributeOptions = $this->getAttribute($attributeCode);
        $attribute = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        $attributeOptions = $attribute ->getSource()->getAllOptions(false); 
        foreach ($attributeOptions AS $attributeOption) {
            //$attrs['gender'][] = array('code'=>$attributeOption['value'],'label'=>$attributeOption['label']);
            $attrs['styles'][] = $attributeOption;
        }
            
        $data['attrs'] = $attrs;
        
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
        
    }
    
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
    
    public function filtersv2Action($categoryId){
        
        $categoryId = (int)$this->getRequest()->getParam('cid');
        if($categoryId){
            $attributes = $this->getAttributesFromCategoryId($categoryId);
            
            
            $attrs = array();
            
            foreach ($attributes as $attribute_code){
                
                
                $attribute = Mage::getModel('catalog/resource_eav_attribute')
                    ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attribute_code);
                $attributeOptions = $attribute ->getSource()->getAllOptions(false); 
                $attributeLabel = $attribute->getStoreLabel();
                foreach ($attributeOptions AS $attributeOption) {
                    //$attrs[$attribute_code][] = $attributeOption;
                    $attrs[$attribute_code]['label'] = $attributeLabel;
                    $attrs[$attribute_code]['options'][] = $attributeOption;
                }
            }
            header("Content-Type: application/json");
            print_r(json_encode($attrs));
            die;
            
        }
    }
    
}