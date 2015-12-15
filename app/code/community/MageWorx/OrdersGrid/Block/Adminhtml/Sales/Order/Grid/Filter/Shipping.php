<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Block_Adminhtml_Sales_Order_Grid_Filter_Shipping extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{

    /**
     * @return array|null
     */
    public function getCondition()
    {
        $value = $this->getValue();
        if (is_null($value)) {
            return null;
        }
        $code = explode('_', $value);
        if (count($code) > 1 && $code[0] == $code[1]) {
            return array('like' => $code[0] . '\_%');
        } else {
            return array('eq' => $value);
        }
    }
}
