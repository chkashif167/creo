<?php

class Progos_Creomob_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        echo "Hello creomob";
    }

    public function categoriesAction() {
        $root = Mage::app()->getStore()->getRootCategoryId();

        $data = array();
        $category = Mage::getModel('catalog/category')->getCategories($root);
        foreach ($category as $c) {
            $cat['id'] = $c->getId();
            $cat['name'] = $c->getName();
            $cat['img'] = $c->getImageUrl();
            $data[] = $cat;
        }

//        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }

    public function categoryAction() {
        $id = $this->getRequest()->getParam('id');
        $data = array();
        $category = Mage::getModel('catalog/category')->getCategories($id);

        foreach ($category as $c) {
            $sc = Mage::getModel('catalog/category')->load($c->getId());
            $cat['id'] = $sc->getId();
            $cat['name'] = $sc->getName();
            $cat['img'] = $sc->getImageUrl();
            $data[] = $cat;
        }

//        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }
    
    public function testAction(){
//        $collection = Mage::getModel('catalog/product')->getCollection();
//        $collection->addAttributeToSelect('name')
//                ->addAttributeToSelect('size')
//                ->addAttributeToSelect('gender');
//        $products = $collection->load();
//        $data = array();
//        
//        foreach($products as $p){
//            $data[] = array("name"=>$p->getName(),"gender"=>$p->getGender());
//        }
        
        $attributeCode = "color";
        $attribute = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        $attributeOptions = $attribute ->getSource()->getAllOptions(false); 
        print_r($attributeOptions);die();
        
        
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }


}