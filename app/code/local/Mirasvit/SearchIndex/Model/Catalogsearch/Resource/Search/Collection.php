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



class Mirasvit_SearchIndex_Model_Catalogsearch_Resource_Search_Collection
    extends Mage_CatalogSearch_Model_Resource_Search_Collection
{
    public function addSearchFilter($query)
    {
        $catalogIndex = Mage::helper('searchindex/index')->getIndex('mage_catalog_product');
        $engine = Mage::helper('searchindex')->getSearchEngine();
        $result = $engine->query($query, null, $catalogIndex);
        $catalogIndex->setMatchedIds($result);
        $catalogIndex->joinMatched($this);

        $this->getSelect()->order('relevance desc');

        return $this;
    }
}
