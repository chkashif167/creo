<?php
/**
 * MagiDev
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MagiDev Package to newer
 * versions in the future. If you wish to customize Package for your
 * needs please refer to http://www.magidev.com for more information.
 *
 * @category    Magidev
 * @package     Magidev_Sort
 * @copyright   Copyright (c) 2014 MagiDev. (http://www.magidev.com)
 */

/**
 * Backend Controller
 *
 * @category   Magidev
 * @package    Magidev_Sort
 * @author     Magidev Team <support@magidev.com>
 */
class Magidev_Sort_Adminhtml_SortproductController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Save  product positions
	 */
    public function saveAction() {
   		$_data=$this->getRequest()->getPost();
        $_model=Mage::getModel('magidev_sort/positions');
		if(!isset($_data['product1'])){
			$categoryId=$_data['categoryId'];
			unset($_data['categoryId']);
			unset($_data['form_key']);
			foreach( $_data as $_id=>$position ){
				$_model->updatePosition($categoryId,$_id,$position,(int)$this->getRequest()->getParam('store', 0));
			}
		} else {
        	$_model->updatePosition($_data['categoryId'],$_data['product1'],$_data['position1'],(int)$this->getRequest()->getParam('store', 0));
        	$_model->updatePosition($_data['categoryId'],$_data['product2'],$_data['position2'],(int)$this->getRequest()->getParam('store', 0));
		}
   	}

	public function addAction(){
		$categoryId=$this->getRequest()->getParam('category');
		$productIds=$this->getRequest()->getParam('products');
		$positions=$this->getRequest()->getParam('positions');
		$_model=Mage::getModel('magidev_sort/positions');
		$_model->addNewPosition($categoryId,$productIds,$positions,$this->getRequest()->getParam('store'));
		$this->getResponse()->setHeader('Content-type','application/json');
		$this->getResponse()->setBody(Zend_Json::encode(array('')));
	}

	public function searchAction(){
		$_collection=Mage::getModel('magidev_sort/search')
				->setCategoryId($this->getRequest()->getParam('category_id'))
				->setQuery($this->getRequest()->getParam('query'))->getCollection($this->getRequest()->getParam('store'));
		$_array=$_collection->toArray();
		$this->getResponse()->setHeader('Content-type','application/json');
		$this->getResponse()->setBody(Zend_Json::encode($_array['items']));
	}

	/**
	 * Quick edit action
	 */
	public function editAction(){
		if( $this->getRequest()->isAjax()&&$this->getRequest()->getParam('id') ){
			try{
				$model=Mage::getModel('catalog/product');
				$params=array('id'=>$this->getRequest()->getParam('id'));
				if($this->getRequest()->getParam('store')){
					$model->setStoreId($this->getRequest()->getParam('store'));
					$params['store']=$this->getRequest()->getParam('store');
				}
				$model->load($this->getRequest()->getParam('id'));
				$model->setSysUrl($this->getUrl('*/*/quickSave',$params));
				$model->setPrice(round($model->getPrice(),2));
				if( $model->getSpecialPrice() ){
					$model->setSpecialPrice(round($model->getSpecialPrice(),2));
				}
				$this->getResponse()->setBody($model->toJson());
			} catch ( Exception $e ){
				$model=Mage::getModel('catalog/product');
				$model->setError($e->getMessage());
				$this->getResponse()->setBody($model->toJson());
			}
		}
	}

	/**
	 * Quick save action
	 */
	public function quickSaveAction(){
		if( $this->getRequest()->isAjax()&&$this->getRequest()->getParam('id') ){
			try{
				$model=Mage::getModel('catalog/product');
				if($this->getRequest()->getParam('store')){
					$model->setStoreId($this->getRequest()->getParam('store'));
				}
				$model->load($this->getRequest()->getParam('id'));
				$model->setName($this->getRequest()->getParam('name'));
				$model->setSku($this->getRequest()->getParam('sku'));
				$model->setShortDescription($this->getRequest()->getParam('short_description'));
				$model->setPrice($this->getRequest()->getParam('price'));
				$model->setSpecialPrice($this->getRequest()->getParam('special_price'));
				$model->save();
				$model->setPrice(Mage::helper('core')->currency($model->getPrice(), true, false));
				$model->setSpecialPrice(round($model->getSpecialPrice(),2));
				$this->getResponse()->setBody($model->toJson());
			} catch ( Exception $e ){
				$model=Mage::getModel('catalog/product');
				$model->setError($e->getMessage());
				$this->getResponse()->setBody($model->toJson());
			}
		}
	}

	/**
	 * Change status action
	 */
	public function statusAction(){
		if( $this->getRequest()->isAjax()&&$this->getRequest()->getParam('id') ){
			try{
				$_status=((Mage::getModel('catalog/product')
								->setStoreId((int)$this->getRequest()->getParam('store', 0))
								->load($this->getRequest()->getParam('id'))->getStatus()==Mage_Catalog_Model_Product_Status::STATUS_ENABLED)?
																						Mage_Catalog_Model_Product_Status::STATUS_DISABLED :
																						Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
				Mage::getModel('catalog/product_status')->updateProductStatus(
						$this->getRequest()->getParam('id'), (int)$this->getRequest()->getParam('store', 0),
						$_status
				);
			} catch( Exception $e ){
				$this->getResponse()->setBody($e->getMessage());
				return;
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($_status));
		}

	}

	/**
	 * Remove product from category
	 */
	public function deleteAction(){
		if( $this->getRequest()->isAjax()&&$this->getRequest()->getParam('id') ){
			try{
				Mage::getModel('magidev_sort/positions')->removePosition($this->getRequest()->getParam('categoryId'),$this->getRequest()->getParam('id'));
			} catch( Exception $e ){
				$this->getResponse()->getBody($e->getMessage());
				return;
			}
			$this->getResponse()->setBody(1);
		}
	}
}