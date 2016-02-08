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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Advd_Block_Adminhtml_Widget_Search_Grid extends Mirasvit_Advd_Block_Adminhtml_Widget_Abstract_Grid
{
    public function getGroup()
    {
        return 'Search';
    }

    public function getName()
    {
        return 'Last Search Terms';
    }

    public function prepareOptions()
    {
        $this->form->addField(
            'limit',
            'text',
            array(
                'name'  => 'limit',
                'label' => Mage::helper('advr')->__('Number Of Search Terms'),
                'value' => $this->getParam('limit', 5)
            )
        );

        return $this;
    }

    protected function _prepareCollection($grid)
    {
        $collection = Mage::getModel('catalogsearch/query')
            ->getResourceCollection();
        $collection->setRecentQueryFilter();

        $grid->setCollection($collection);

        return $this;
    }

    protected function _prepareColumns($grid)
    {
        $grid->addColumn('search_query', array(
            'header'   => Mage::helper('advr')->__('Search Term'),
            'sortable' => false,
            'index'    => 'query_text',
            'renderer' => 'adminhtml/dashboard_searches_renderer_searchquery',
        ));

        $grid->addColumn('num_results', array(
            'header'   => Mage::helper('advr')->__('Results'),
            'sortable' => false,
            'index'    => 'num_results',
            'type'     => 'number'
        ));

        $grid->addColumn('popularity', array(
            'header'   => Mage::helper('advr')->__('Number of Uses'),
            'sortable' => false,
            'index'    => 'popularity',
            'type'     => 'number'
        ));

        $grid->setFilterVisibility(false);
        $grid->setPagerVisibility(false);
        $grid->setDefaultLimit($this->getParam('limit', 5));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_search/edit', array('id' => $row->getId()));
    }
}
