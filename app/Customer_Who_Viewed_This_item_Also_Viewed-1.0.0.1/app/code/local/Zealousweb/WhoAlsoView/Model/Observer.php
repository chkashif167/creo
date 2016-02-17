<?php
session_start();
class Zealousweb_WhoAlsoView_Model_Observer
{

			public function WhoAlsoView(Varien_Event_Observer $observer)
			{   
                $getSession = $_SESSION['MosViewUser']; 
                if(!isset($getSession))
                {
                    $cus_session = $this->generateRandomString();
                    $_SESSION['MosViewUser'] = $cus_session;
                    $getSession = $cus_session;   
                }
                 $id = $observer->getEvent()->getProduct()->getId();
                 $product = $observer->getEvent()->getProduct();
                 $name = $product->getName();
                 $cat_id = $product->getCategoryIds();  
                 $cur_category_id = implode(",",$cat_id);                       
                 $sku = $product->getSku();
                 $ip = $_SERVER['REMOTE_ADDR'];
                 $model = Mage::getModel('whoalsoview/whoalsoview');
                    
                if(!isset($_SESSION[$getSession]))
                {
                    $user_product_view_array = array();
                    array_push($user_product_view_array, $id);
                    $_SESSION[$getSession] = $user_product_view_array;
                    $model->setSessionCod($getSession); //session_cod field
                    $model->setProductId($id); //product_id
                    $model->setProductSku($sku); //product_sku
                    $model->setProductCategories($cur_category_id);                                    
                    $model->setIp($ip);
                    $model->save();
                }
                else
                {
                    if(!in_array($id,$_SESSION[$getSession]))
                    {
                       array_push($_SESSION[$getSession], $id);   
                       $model->setSessionCod($getSession); //session_cod field
                       $model->setProductId($id); //product_id
                       $model->setProductSku($sku); //product_sku
                       $model->setProductCategories($cur_category_id);     
                       $model->setIp($ip);
                       $model->save();                                         
                    }                    
                                    
                }                           
			}

            public function generateRandomString($length = 5) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randomString = '';
             for ($i = 0; $i < $length; $i++) {
                     $randomString .= $characters[rand(0, strlen($characters) - 1)];
             }
                 return $randomString.time();
            }
		
}
