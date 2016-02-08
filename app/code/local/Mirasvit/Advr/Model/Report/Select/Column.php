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


class Mirasvit_Advr_Model_Report_Select_Column extends Varien_Object
{
    protected $filter;

    public function _construct()
    {
        $this->setGrid(new Varien_Object());

        return $this;
    }

    public function getFilterHtml()
    {
        return $this->getFilter()->getHtml();
    }

    protected function _getFilterByType()
    {
        $type = strtolower($this->getType());

        $classes = array(
            'datetime'   => 'adminhtml/widget_grid_column_filter_datetime',
            'date'       => 'adminhtml/widget_grid_column_filter_date',
            'range'      => 'adminhtml/widget_grid_column_filter_range',
            'number'     => 'adminhtml/widget_grid_column_filter_range',
            'currency'   => 'adminhtml/widget_grid_column_filter_range',
            'price'      => 'adminhtml/widget_grid_column_filter_price',
            'country'    => 'adminhtml/widget_grid_column_filter_country',
            'options'    => 'adminhtml/widget_grid_column_filter_select',
            'massaction' => 'adminhtml/widget_grid_column_filter_massaction',
            'checkbox'   => 'adminhtml/widget_grid_column_filter_checkbox',
            'radio'      => 'adminhtml/widget_grid_column_filter_radio',
            'store'      => 'adminhtml/widget_grid_column_filter_store',
            'theme'      => 'adminhtml/widget_grid_column_filter_theme',
        );

        if (isset($classes[$type])) {
            return $classes[$type];
        }

        return 'adminhtml/widget_grid_column_filter_text';
    }

    public function getFilter()
    {
        if (!$this->filter) {
            $filterClass = $this->_getFilterByType();

            $this->filter = Mage::app()->getLayout()->createBlock($filterClass)
                ->setColumn($this)
                ->setValue($this->getValue());
        }

        return $this->filter;
    }
}
