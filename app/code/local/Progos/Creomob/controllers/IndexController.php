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

        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }

    
    
    

}