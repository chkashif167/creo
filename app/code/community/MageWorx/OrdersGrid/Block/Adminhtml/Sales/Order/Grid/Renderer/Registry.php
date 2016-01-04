<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Block_Adminhtml_Sales_Order_Grid_Renderer_Registry extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * @param Varien_Object $row
     * @return array|mixed|string
     */
    public function render(Varien_Object $row)
    {
        $index = $this->getColumn()->getIndex();
        switch ($index) {
            case 'method':
                $registry = 'payment_methods';
                break;
            case 'shipping_method':
                $registry = 'shipping_methods';
                break;
            case 'customer_group_id':
                $registry = 'customer_groups';
                break;
            case 'shipped':
                $registry = 'shipped_statuses';
                break;
            case 'order_group_id':
                $registry = 'order_groups';
                break;
            case 'is_edited':
                $registry = 'edited_statuses';
                break;
            default :
                return '';
        }
        $id = $row->getData($index);
        $values = Mage::registry($registry);

        if ($index == 'shipping_method' && $row->getData('shipping_description')) {
            return $row->getData('shipping_description');
        }

        if (isset($values[$id])) {
            return $this->htmlEscape($values[$id]);
        }

        if ($index == 'shipping_method' && strpos($id, '_')) {
            $id = explode('_', $id);
            $id2 = $id[0] . '_' . $id[0];
            unset($id[0]);
            if (isset($values[$id2])) {
                return $this->htmlEscape($values[$id2] . ' ' . implode('_', $id));
            }
        }
        return $id;
    }

}
