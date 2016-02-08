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



class Mirasvit_Advr_Block_Adminhtml_Block_Chart_Column extends Mirasvit_Advr_Block_Adminhtml_Block_Chart_Abstract
{
    public function _prepareLayout()
    {
        $this->setTemplate('mst_advr/block/chart/column.phtml');

        return parent::_prepareLayout();
    }

    public function getDataTable()
    {
        $array = array();

        $columns = $this->getColumns();

        $row = array();

        $row[] = $this->getXAxisType();

        foreach ($columns as $index => $column) {
            if ($this->_isColumnAllowed($column)) {
                $row[] = $column->getHeader();
            }
        }

        $array[] = ($row);

        foreach ($this->getCollection() as $itm) {
            $row = array();

            if (isset($columns[$this->getXAxisField()])) {
                $xColumn = $columns[$this->getXAxisField()];
                $row[] = str_replace('<br>', "\n", $xColumn->getRowField($itm));
            }


            foreach ($columns as $index => $column) {
                if ($this->_isColumnAllowed($column)) {
                    $value = floatval($itm->getData($index));

                    $row[] = $value;
                }
            }

            $array[] = ($row);
        }

        return $array;
    }

    public function getDefaultSeries()
    {
        $series = array();

        $idx = 1;
        foreach ($this->getColumns() as $column) {
            if (!in_array($column->getType(), array('number', 'currency'))) {
                continue;
            }

            if ($column->getChart() === 'none') {
                continue;
            }

            if ($column->getChart()) {
                $series[] = $idx;
            }

            $idx++;
        }

        return $series;
    }
}
