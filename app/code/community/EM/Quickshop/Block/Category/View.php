<?php
class EM_Quickshop_Block_Category_View extends Mage_Catalog_Block_Category_View
{
    public function getProductListHtml()
    {
        return $this->getChildHtml('product_list');
    }
}