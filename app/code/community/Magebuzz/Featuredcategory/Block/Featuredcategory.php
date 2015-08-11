<?php
class Magebuzz_Featuredcategory_Block_Featuredcategory extends Mage_Core_Block_Template
{
    public function __construct() {
        parent::__construct();
        $collection = Mage::getModel('featuredcategory/featuredcategory')->getCollection();
        $collection->addFieldToFilter('featured_category', 1);
        return parent::_prepareLayout();
    }
    
     public function getFeaturedcategory()     
     { 
        if (!$this->hasData('featuredcategory')) {
            $this->setData('featuredcategory', Mage::registry('featuredcategory'));
        }
        return $this->getData('featuredcategory');
        
     }

    public function getFeatureCategoryCollection(){
        $collection = Mage::getModel('featuredcategory/featuredcategory')->getCollection();
        $collection->addFieldToFilter('featured_category', 1);

        return $collection;
    }
}