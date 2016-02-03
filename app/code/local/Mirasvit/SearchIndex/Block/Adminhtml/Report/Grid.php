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



class Mirasvit_SearchIndex_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        $this->setId('searchindex_report_grid');
        $this->setSaveParametersInSession(false);
        $this->setDefaultSort('popularity');
        $this->setDefaultDir('desc');
        parent::__construct();
    }

    protected function _prepareCollection()
    {
        $store = (!is_null($this->getParam('store'))) ? $this->getParam('store')
            : Mage::app()->getDefaultStoreView()->getId();

        $collection = Mage::getResourceModel('catalogsearch/query_collection')
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('store_id', $store);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('query_text', array(
            'header' => Mage::helper('searchindex')->__('Search Query'),
            'index' => 'query_text',
            'width' => '100px',
            'renderer' => 'Mirasvit_SearchIndex_Block_Adminhtml_Report_Renderer',
            'sortable' => true,
            'column_css_class' => 'a-center',
        ));

        $this->addColumn('popularity', array(
            'header' => Mage::helper('searchindex')->__('Popularity'),
            'index' => 'popularity',
            'width' => '30px',
            'type' => 'range',
            'column_css_class' => 'a-center',
        ));

        $this->addColumn('num_results', array(
            'header' => Mage::helper('searchindex')->__('Number of results'),
            'index' => 'num_results',
            'width' => '30px',
            'type' => 'range',
            'column_css_class' => 'a-center',
            'frame_callback' => array(new Mirasvit_SearchIndex_Block_Adminhtml_Report_Renderer(), 'getResultsCount'),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('searchindex')->__('View search results'),
            'width' => '15px',
            'sortable' => false,
            'filter' => false,
            'type' => 'action',
            'align' => 'center',
            'header_css_class' => 'a-center',
            'frame_callback' => array(new Mirasvit_SearchIndex_Block_Adminhtml_Report_Renderer(), 'getSearchResultsUrl'),
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('searchindex')->__('First '.$this->getProductCollectionLimit().' products'),
            'filter' => false,
            'sortable' => false,
            'frame_callback' => array(new Mirasvit_SearchIndex_Block_Adminhtml_Report_Renderer(), 'getSearchResultsGrid'),
        ));

        return parent::_prepareColumns();
    }

    private function getProductCollectionLimit()
    {
        return Mage::getStoreConfig('catalog/frontend/grid_per_page');
    }
}
