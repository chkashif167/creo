<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_System_Config_Source_Orders_Status
{
    /**
     * @param bool|true $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect=true) {
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        $options = array();
        foreach ($statuses as $code=>$label) {
            $options[] = array('value'=>$code, 'label'=>$label);
        }
        return $options;        
    }
}