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



/**
 * Блок вывода результатов поиска. Основная задача - дочерние блоки всех включенных индексов, ограничить кол-во выводимых елементов.
 *
 * @category Mirasvit
 */
class Mirasvit_SearchAutocomplete_Block_Result extends Mage_Catalog_Block_Product_Abstract
{
    protected $_collections = array();
    protected $_indexes = array();

    public function _prepareLayout()
    {
        $this->setTemplate('mst_searchautocomplete/autocomplete/result.phtml');

        return parent::_prepareLayout();
    }

    public function init()
    {
        $this->_prepareIndexes();
    }

    protected function _prepareIndexes()
    {
        // Mage::dispatchEvent('searchautocomplete_prepare_collection');

        if (!Mage::getStoreConfig('searchautocomplete/general/indexes')) {
            $this->_indexes = Mage::helper('searchautocomplete')->getIndexes(true);
        } else {
            $this->_indexes = Mage::helper('searchautocomplete')->getIndexes(false);
        }

        $maxCount = Mage::getStoreConfig('searchautocomplete/general/max_results');
        $perIndex = ceil($maxCount / count($this->_indexes));
        $sizes = array();
        $additional = 0;
        foreach ($this->_indexes as $index => $label) {
            $st = microtime(true);
            $size = $this->getCollection($index)->getSize();

            if ($size >= $perIndex) {
                $sizes[$index] = $perIndex;
            } else {
                $additional = $perIndex - $size;
                $sizes[$index] = $size;
            }

            if ($size == 0) {
                unset($this->_indexes[$index]);
            }

            if ($this->getIndexFilter() && $index != $this->getIndexFilter()) {
                unset($this->_indexes[$index]);
            }
        }

        $additional = $this->_indexes ? ceil($additional / count($this->_indexes)) : 0;
        foreach ($this->_indexes as $index => $label) {
            $sizes[$index] += $additional;
        }

        foreach ($sizes as $index => $size) {
            $this->getCollection($index)->setPageSize($size);
        }
    }

    public function getIndexes()
    {
        return $this->_indexes;
    }

    public function getCollection($index)
    {
        if (!isset($this->_collections[$index])) {
            if (Mage::helper('core')->isModuleEnabled('Mirasvit_SearchIndex')) {
                $model = Mage::helper('searchindex/index')->getIndex($index);
                $collection = $model->getCollection();
            } elseif (Mage::helper('core')->isModuleEnabled('Enterprise_Search')) {
                $collection = Mage::getSingleton('enterprise_search/search_layer')->getProductCollection();
            } else {
                $collection = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
            }

            if ($index != 'mage_catalog_attribute' && !Mage::helper('core')->isModuleEnabled('Enterprise_Search')) {
                $collection->getSelect()->order('relevance desc');
            }

            if ($index == 'mage_catalog_product' && $this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                $collection->addCategoryFilter($category);
            }

            $this->_collections[$index] = $collection;
        }

        return $this->_collections[$index];
    }

    public function getItemHtml($index, $item)
    {
        $block = Mage::app()->getLayout()->createBlock('searchautocomplete/result')
            ->setTemplate('mst_searchautocomplete/autocomplete/index/'.str_replace('_', '/', $index).'.phtml')
            ->setItem($item);

        return $block->toHtml();
    }

    public function getProductShortDescription($product)
    {
        $shortDescription = $product->getShortDescription();
        if (Mage::helper('mstcore')->isModuleInstalled('Mirasvit_Seo') &&
            method_exists(Mage::helper('seo'), 'getCurrentSeoShortDescriptionForSearch')
        ) {
            if ($seoShortDescription = Mage::helper('seo')->getCurrentSeoShortDescriptionForSearch($product)) {
                $shortDescription = $seoShortDescription;
            }
        }

        return $shortDescription;
    }
}
