<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Edit_Form_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->removeColumn('action');
        $this->_exportTypes = array();

        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/customersGrid', array('_current'=> true));
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return 'submit_customer_'.$row->getId();
    }
}