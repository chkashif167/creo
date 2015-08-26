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

    public function categoryFilterTreeAction() {
        $categories = Mage::getModel('catalog/category')
                        ->getCollection()
                        ->addAttributeToSelect('*')
                        ->addIsActiveFilter()
                        ->addLevelFilter(3)
                        ->addAttributeToFilter('level', array( 'gt' => 1))
                        ->addAttributeToFilter('is_active', '1')
                        ->addAttributeToFilter('include_in_menu', '1')
                        ->addAttributeToSort('path', 'asc')
                        ->load()->toArray();
        header("Content-Type: application/json");
        print_r(json_encode($categories));
        die;
    }


}