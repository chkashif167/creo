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



class Mirasvit_SearchIndex_Block_RelatedTerms extends Mage_Core_Block_Template
{
    public function isEnabled()
    {
        return Mage::getSingleton('searchindex/config')->isRelatedTermsEnabled();
    }

    public function getSuggestedCollection()
    {
        $helper = Mage::helper('catalogsearch');
        $collection = Mage::getResourceModel('catalogsearch/query_collection')
            ->setStoreId(Mage::app()->getStore()->getStoreId())
            ->setQueryFilter($helper->getQueryText())
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('num_results', array('gteq' => 1))
            ->addFieldToFilter('query_text', array('neq' => $helper->getQueryText()));
        $collection->getSelect()->limit(5);

        return $collection;
    }

    public function getQueryUrl($query)
    {
        return Mage::getUrl('catalogsearch/result', array('_query' => array('q' => $query->getQueryText())));
    }
}
