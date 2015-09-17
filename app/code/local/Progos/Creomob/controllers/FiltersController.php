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
        
        $attributeCode = "color";
        $attributeOptions = $this->getAttribute($attributeCode);
        $attrs = array();
        foreach ($attributeOptions AS $attributeOption) {
            //$attrs['color'][] = array('code'=>$attributeOption['value'],'label'=>$attributeOption['label']);
            $attrs['color'][] = $attributeOption;
        }
        
        $attributeCode = "size";
        $attributeOptions = $this->getAttribute($attributeCode);
        foreach ($attributeOptions AS $attributeOption) {
            //$attrs['size'][] = array('code'=>$attributeOption['value'],'label'=>$attributeOption['label']);
            $attrs['size'][] = $attributeOption;
        }
        
        $attributeCode = "gender";
        $attributeOptions = $this->getAttribute($attributeCode);
        foreach ($attributeOptions AS $attributeOption) {
            //$attrs['gender'][] = array('code'=>$attributeOption['value'],'label'=>$attributeOption['label']);
            $attrs['gender'][] = $attributeOption;
        }
        
        $attributeCode = "styles";
        $attributeOptions = $this->getAttribute($attributeCode);
        foreach ($attributeOptions AS $attributeOption) {
            //$attrs['gender'][] = array('code'=>$attributeOption['value'],'label'=>$attributeOption['label']);
            $attrs['styles'][] = $attributeOption;
        }
            
        $data['attrs'] = $attrs;
        
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
        
    }
    
}