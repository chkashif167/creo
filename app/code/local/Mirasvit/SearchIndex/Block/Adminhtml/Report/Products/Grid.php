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



class Mirasvit_SearchIndex_Block_Adminhtml_Report_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('searchindex_report_products_grid');
        $this->setSaveParametersInSession(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
        $this->setCollection($this->getSearchResultsCollection());

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header' => Mage::helper('searchindex')->__('Product Name'),
            'index' => 'name',
            'width' => '200px',
            'sortable' => false,
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('searchindex')->__('SKU'),
            'index' => 'sku',
            'width' => '120px',
            'sortable' => false,
        ));

        $this->addColumn('relevance', array(
            'header' => Mage::helper('searchindex')->__('Relevance'),
            'index' => 'relevance',
            'width' => '50px',
            'sortable' => false,
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('searchindex')->__('Action'),
            'width' => '15px',
            'sortable' => false,
            'filter' => false,
            'type' => 'action',
            'getter' => 'getId',
            'align' => 'center',
            'header_css_class' => 'a-center',
            'actions' => array(
                array(
                    'url' => array('base' => 'adminhtml/catalog_product/edit/'),
                    'caption' => $this->helper('catalog')->__('View'),
                    'field' => 'id',
                ),
            ),
        ));

        return parent::_prepareColumns();
    }
}
