<?php
class Tentura_Ngroups_Block_Ngroups extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        $store_id = Mage::app()->getStore()->getStoreId();
        $groups = Mage::getModel('ngroups/ngroups')
                ->getCollection()
                ->addFieldToFilter('visible', '1')
                ->addFieldToFilter('store_ids', array('finset' => $store_id))
                ->toArray();
        $showAll = true;
        if (Mage::registry('current_category')){
            $currentCategory = Mage::registry('current_category');
            $checkGroups = Mage::getModel('ngroups/ngroups')
                    ->getCollection()
                    ->addFieldToFilter('visible', '1')
                    ->addFieldToFilter('category_id', $currentCategory->getId())
                    ->addFieldToFilter('store_ids', array('finset' => $store_id));
            foreach ($checkGroups as $checkGroup){
                if ($checkGroup->getCategoriesHide()){
                    $showAll = false;
                }
            }
        }
        if ($showAll){
            $this->groups = $groups;
        }else{
            $this->groups = $checkGroups->toArray();
        }
        return parent::_prepareLayout();
    }
    
}