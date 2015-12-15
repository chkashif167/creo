<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_Resource_Order_Collection extends Mage_Sales_Model_Mysql4_Order_Collection
{

    /**
     * @param int $days
     * @return $this
     */
    public function setFilterOrdersNoGroup($days = 0)
    {
        if ($this->getSelect()!==null) {                        
            $where  = 'main_table.`order_group_id` = 0';
            if ($days > 0) {
                $where.=  ' AND main_table.`created_at` <= (NOW() - INTERVAL '.$days.' DAY)';
            }
            
            $this->getSelect()->where(new Zend_Db_Expr($where))                
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(new Zend_Db_Expr('main_table.`entity_id`'));            
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function hideDeletedGroup()
    {
        if ($this->getSelect()!==null) {                        
            $this->getSelect()->where('main_table.`order_group_id` <> 2');
        }                        
        return $this;
    }

}
