<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */


class Amasty_Checkoutfees_Block_Adminhtml_Fees_Grid_Filter_Stores extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Store
{
    public function getCondition()
    {
        $value = $this->getValue();
        if (is_null($value)) {
            return null;
        }

        return array('or' => array('like' => '%,' . $value . ',%'));
    }
}