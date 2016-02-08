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



class Mirasvit_SearchIndex_Model_Index_Mage_Catalog_Category_Indexer extends Mirasvit_SearchIndex_Model_Indexer_Abstract
{
    protected function _getSearchableEntities($storeId, $entityIds, $lastEntityId, $limit = 100)
    {
        $rootCategoryId = Mage::app()->getStore($storeId)->getRootCategoryId();
        $rootCategory = Mage::getModel('catalog/category')->load($rootCategoryId);

        $collection = Mage::getResourceModel('catalog/category_collection');
        $collection
            ->setStoreId($storeId)
            ->addFieldToFilter('is_active', 1)
            ->addNameToResult()
            ->addPathFilter($rootCategory->getPath().'/');

        $collection->getSelect()->where('e.entity_id > ?', $lastEntityId)
            ->limit($limit)
            ->order('e.entity_id');

        foreach ($this->getIndexModel()->getAttributes() as $attr => $weight) {
            $collection->addAttributeToSelect($attr)
                ->addAttributeToSort($attr, 'ASC');
        }

        // static block attached to category
        $collection->addAttributeToSelect('landing_page')
            ->addAttributeToSort('landing_page', 'ASC');

        if ($entityIds) {
            $collection->addFieldToFilter('entity_id', array('in' => $entityIds));
        }

        $collection->getSelect()->group('entity_id');

        foreach ($collection as $category) {
            $helper = Mage::helper('cms');
            $processor = $helper->getPageTemplateProcessor();

            $category->setDescription($processor->filter($category->getDescription()));

            // добавляем контент стачиного блока, связанго с категорией
            if ($category->getLandingPage()) {
                $cmsBlock = Mage::getModel('cms/block')->load($category->getLandingPage());
                $text = $cmsBlock->getContent();

                $category->setDescription($category->getDescription().' '.$text);
            }
        }

        return $collection;
    }
}
