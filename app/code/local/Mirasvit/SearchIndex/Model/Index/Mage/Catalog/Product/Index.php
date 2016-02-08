<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_SearchIndex_Model_Index_Mage_Catalog_Product_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'Magento';
    }

    public function getBaseTitle()
    {
        return 'Catalog Products';
    }

    public function getPrimaryKey()
    {
        return 'product_id';
    }

    public function getIsActive()
    {
        return true;
    }

    public function isLocked()
    {
        return true;
    }

    public function getSearchTabs()
    {
        $searchTabs = array();

        $this->setStoreId(Mage::app()->getStore()->getId());
        $searchTabs[$this->getIndexCode()] = $this;

        if (Mage::getSingleton('searchindex/config')->isMultiStoreResultsEnabled()) {
            $allowedStores = Mage::getSingleton('searchindex/config')->getEnabledMultiStores();

            foreach (Mage::app()->getStores() as $store) {
                if (Mage::app()->getStore()->getId() != $store->getId()
                    && in_array($store->getId(), $allowedStores)
                ) {
                    $indexModel = clone $this;
                    $indexModel->setTitle($store->getFrontendName())
                        ->setStoreId($store->getId());
                    $searchTabs[$this->getIndexCode() . $store->getId()] = $indexModel;
                }
            }
        }

        return $searchTabs;
    }

    public function getFieldsets()
    {
        return array(
            'Mage_Catalog_Product_Additional',
        );
    }

    public function getAvailableAttributes()
    {
        $result = array();
        $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');
        $productAttributeCollection->addIsSearchableFilter();
        foreach ($productAttributeCollection as $attribute) {
            $result[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $result['tags'] = __('Tags');
        $result['category_name'] = __('Category');
        $result['category_description'] = __('Category Description');
        $result['category_meta_title'] = __('Category Meta Title');
        $result['category_meta_keywords'] = __('Category Meta Keywords');
        $result['category_meta_description'] = __('Category Meta Description');

        return $result;
    }

    /**
     * After process search, we save count search results to query.
     *
     * @var string  $queryText
     * @var integer $storeId
     *
     * @return $this
     */
    protected function _processSearch($queryText, $storeId)
    {
        parent::_processSearch($queryText, $storeId);

        $query = Mage::helper('catalogsearch')->getQuery();
        $query->setNumResults(count($this->_matchedIds[$queryText]))
            ->setIsProcessed(1)
            ->save();

        return $this;
    }

    public function getCollection()
    {
        $matchedIds = $this->getMatchedIds(null, $this->getStoreId());
        $collection = Mage::getModel('catalogsearch/layer')->getProductCollection();

        return $collection;
    }

    public function getCountResults()
    {
        if (Mage::app()->getStore()->getId() == $this->getStoreId()) {
            if (Mage::registry('current_layer')) {
                return Mage::registry('current_layer')->getProductCollection()->getSize();
            } else {
                return parent::getCountResults();
            }
        } else {
            $matchedIds = $this->getMatchedIds(null, $this->getStoreId());

            return count($matchedIds);
        }
    }
}
