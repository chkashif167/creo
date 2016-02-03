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



class Mirasvit_Advr_Block_Adminhtml_Block_Grid_Column extends Mage_Adminhtml_Block_Widget_Grid_Column
{
    public function getRowFieldExport(Varien_Object $row)
    {
        $renderedValue = $row->getData($this->getIndex());

        # if need format column value
        $exportCallback = $this->getExportCallback();
        if (is_array($exportCallback)) {
            $renderedValue = call_user_func($exportCallback, $renderedValue, $row, $this, false);
        }

        $renderedValue = strip_tags($renderedValue);

        return $renderedValue;
    }

    public function getRowField(Varien_Object $row)
    {
        $value = parent::getRowField($row);

        if ($this->getLinkCallback()) {
            $url = call_user_func($this->getLinkCallback(), $row);

            # we use # for separate image and text (country column)
            $value = explode('#', $value);
            if (count($value) == 2) {
                return $value[0] . '<a href="' . $url . '">' . $value[1] . '</a>';
            } else {
                return '<a href="' . $url . '">' . $value[0] . '</a>';
            }
        } else {
            $value = str_replace('#', ' ', $value);
        }

        return $value;
    }
}
