<?php

class Zealousweb_WhoAlsoView_Block_Whoalsoview extends Mage_Catalog_Block_Product_View
{    
    protected $getsetting = null;
    
    public function __construct() {   
        $this->getsetting = Mage::getSingleton('whoalsoview/getsetting');
    }    
    
    public function lastviewproduct($pro_sku,$cat_id)
    {
        $product_sku_count = array();
        if(!($this->getsetting->isEnabled())) {
            return $product_sku_count;
        }
        $user_session = $_SESSION['MosViewUser'];
        $Model = Mage::getModel('whoalsoview/whoalsoview');
        $connection = $Model->getCollection();
        $connection->addFilter('product_sku', $pro_sku);
        $connection->addFieldToFilter('session_cod',array('neq'=>$user_session)); 
        $all_data = $connection->getData();
        $product_session = array();
        foreach($all_data as $data)
        {
            $connection_2 = $Model->getCollection();
            $connection_2->addFilter('session_cod', $data['session_cod']);
            $connection_2->addFieldToFilter('product_sku',array('neq'=>$pro_sku)); 
            $connection_2->getData();

            array_push($product_session, $connection_2->getData());
        } 
       
        $product_sku_array = array();
        foreach($product_session as $key => $prodct_data){
            foreach($prodct_data as $step_prodct){
                $cat_data_id =  explode(',',$step_prodct['product_categories']); 
                $result = array_intersect($cat_id, $cat_data_id);
                if($this->getsetting->getshowCatProductOnly()){ 
                if(!empty($result)){ array_push($product_sku_array, $step_prodct['product_id']); }
                }
                else {
                   array_push($product_sku_array, $step_prodct['product_id']);
                }
            }
        }
        $product_sku_array_mini = $product_sku_array;
        foreach($product_sku_array_mini as $key=>$procuts_array)
        {
                $tmp = array_count_values($product_sku_array);
                $cnt = $tmp[$procuts_array]; 
                $product_sku_count[$procuts_array]=$cnt;
        }
        arsort($product_sku_count);
        return $product_sku_count;  
    }
    
    public function displaytitle()
    {
       return $this->getsetting->getDisplayTitle();
    }
    
    public function displayproductlength()
    {
       return $this->getsetting->getMaxProductDisplay();
    }
    
    public function showInStock()
    {
       return $this->getsetting->getshowInStockProduct();
    }
   
    
}