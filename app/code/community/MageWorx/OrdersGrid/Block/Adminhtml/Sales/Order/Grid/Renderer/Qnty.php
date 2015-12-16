<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Block_Adminhtml_Sales_Order_Grid_Renderer_Qnty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * @param Varien_Object $row
     * @return array|mixed|string
     */
    public function render(Varien_Object $row)
    {
        /** @var MageWorx_OrdersGrid_Helper_Data $helper */
        $helper = Mage::helper('mageworx_ordersgrid');

        $data = array();
        $data[] = $helper->__('Ordered').'&nbsp;'.intval($row->getData('total_qty_ordered_agregated'));
        
        $total = intval($row->getData('total_qty_canceled'));
        if ($total>0) {
            $data[] = $helper->__('Canceled').'&nbsp;'.$total;
        }

        $total = intval($row->getData('total_qty_invoiced'));
        if ($total>0) {
            $data[] = $helper->__('Invoiced').'&nbsp;'.$total;
        }

        $total = intval($row->getData('total_qty_shipped'));
        if ($total>0) {
            $data[] = $helper->__('Shipped').'&nbsp;'.$total;
        }
        
        $total = intval($row->getData('total_qty_refunded'));
        if ($total>0) {
            $data[] = $helper->__('Refunded').'&nbsp;'.$total;
        }
        
        $data = implode('<br/>', $data);        
        if (strpos(Mage::app()->getRequest()->getRequestString(), '/exportCsv/')) {
            $data = str_replace(array('&nbsp;','<br/>'), array(' ','|'), $data);
        }
        return $data;      
    }
}
