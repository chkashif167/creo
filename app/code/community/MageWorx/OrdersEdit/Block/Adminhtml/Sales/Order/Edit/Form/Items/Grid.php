<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Edit_Form_Items_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('productGrid');

        $this->setRowClickCallback('orderEditItems.productGridRowClick.bind(orderEditItems)');
        $this->setCheckboxCheckCallback('orderEditItems.productGridCheckboxCheck.bind(orderEditItems)');
        $this->setRowInitCallback('orderEditItems.productGridRowInit.bind(orderEditItems)');
    }

    /**
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumnAfter('type',
            array(
                'header' => Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type' => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ), 'name');

        return parent::_prepareColumns();
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
        return $this->getUrl('*/*/productGrid', array('_current' => true));
    }

    /**
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        $order = $this->getData('order');
        return Mage::app()->getStore($order->getStoreId());
    }
}