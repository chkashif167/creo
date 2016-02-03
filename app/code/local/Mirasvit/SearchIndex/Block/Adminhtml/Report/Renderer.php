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



class Mirasvit_SearchIndex_Block_Adminhtml_Report_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = '<b>'.strip_tags($row->getQueryText()).'</b>';

        return $value;
    }

    private function getSearchResultsCollection($row, $limit = null)
    {
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
            $row->getStoreId(),
            Mage_Core_Model_App_Area::AREA_FRONTEND
        );

        $queryHelper = Mage::helper('catalogsearch');

        $this->getRequest()->setParam($queryHelper->getQueryParamName(), $row->getQueryText());

        $searchResultCollection = Mage::getModel('catalogsearch/fulltext')->getResourceCollection();
        Mage::getModel('catalogsearch/layer')->prepareProductCollection($searchResultCollection);
        $searchResultCollection->getSelect()->group('e.entity_id');
        //$searchResultCollection->getSelect()->order('relevance desc');
        $searchResultCollection->setPageSize($limit)
            ->setCurPage(1);
        $searchResultCollection->loadData();

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $searchResultCollection;
    }

    public function getSearchResultsUrl($value, $row, $column, $isExport)
    {
        $url = Mage::app()->getStore($row->getStoreId())->getUrl('catalogsearch/result', array('_query' => array('q' => $row->getQueryText())));
        $value = '<a href="'.$url.'" target="_blank">View</a>';

        return $value;
    }

    public function getSearchResultsGrid($value, $row, $column, $isExport)
    {
        $limit = filter_var($column->getHeader(), FILTER_SANITIZE_NUMBER_INT);
        $collection = $this->getSearchResultsCollection($row, $limit);

        $productsGrid = $column->getGrid()->getLayout()
            ->createBlock('searchindex/adminhtml_report_products_grid')
            ->setSearchResultsCollection($collection);

        return $productsGrid->toHtml();
    }

    public function getResultsCount($value, $row, $column, $isExport)
    {
        $collection = $this->getSearchResultsCollection($row);

        return ($collection->count() > 0) ? $collection->count() : '0';
    }
}
