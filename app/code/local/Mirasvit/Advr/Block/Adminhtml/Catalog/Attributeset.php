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



class Mirasvit_Advr_Block_Adminhtml_Catalog_Attributeset extends Mirasvit_Advr_Block_Adminhtml_Catalog_Abstract
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Sales by Attribute Set'));

        return $this;
    }

    protected function prepareChart()
    {
        $this->setChartType('column');

        $this->initChart()
            ->setXAxisType('category')
            ->setXAxisField('product_attribute_set_id');

        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid()
            ->setDefaultSort('sum_item_row_total')
            ->setDefaultDir('desc')
            ->setDefaultLimit(1000)
            ->setPagerVisibility(false);

        return $this;
    }

    protected function _prepareCollection()
    {
        $attribute = $this->getFilterData()->getGroupByAttribute();

        if (!$attribute) {
            $attribute = 'status';
        }

        $collection = Mage::getModel('advr/report_sales')
            ->setBaseTable('catalog/product')
            ->setFilterData($this->getFilterData())
            ->selectColumns($this->getVisibleColumns())
            ->groupByColumn('product_attribute_set_id');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'product_attribute_set_id' => array(
                'header'          => 'Attribute Set',
                'type'            => 'options',
                'options'         => Mage::getModel('advr/report_sales')->getAttributeSetOptions(),
                'totals_label'    => 'Total',
                'chart'           => true,
                'export_callback' => array($this, 'frameCallbackAttributeSet'),
            ),
        );

        $columns += $this->getBaseProductColumns(true);

        $columns['actions'] = array(
            'header'  => 'Actions',
            'actions' => array(
                array(
                    'caption'  => Mage::helper('advr')->__('Detail'),
                    'callback' => array($this, 'detailUrlCallback')
                ),
            ),
        );

        return $columns;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function frameCallbackAttributeSet($value, $row, $column)
    {
        return Mage::getModel('eav/entity_attribute_set')->load($value)->getAttributeSetName();
    }

    public function detailUrlCallback($row)
    {
        $url = $this->getUrl(
            'adminhtml/advr_catalog/attributesetDetail',
            array('attribute_set_id' => $row->getProductAttributeSetId())
        );

        return $url;
    }
}
