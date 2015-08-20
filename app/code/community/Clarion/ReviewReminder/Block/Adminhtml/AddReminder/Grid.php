<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    14th Jan, 2015
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    add review reminder grid block 
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Clarion_ReviewReminder_Block_Adminhtml_AddReminder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('addReminderGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    
    /*
     * Customer name filter to the grid
     * @param object $collection
     * @param object $column
     * @return Clarion_ReviewReminder_Block_Adminhtml_Reviewreminder_Grid
     */
    protected function _customerNameCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        
        $customerFullName = $value;
        $arrCustomerFullName = explode(' ', $customerFullName);
        
        $customerFirstName = isset($arrCustomerFullName[0])? $arrCustomerFullName[0]: '';
        $customerLastName = isset($arrCustomerFullName[1])? $arrCustomerFullName[1]: '';
        
        if(!empty($customerFirstName)){
            $collection->getSelect()
                ->where("customer_firstname like '%" . $customerFirstName."%'"); 
        }
        
        if(!empty($customerLastName)){
            $collection->getSelect()
                ->where("customer_lastname like '%" . $customerLastName."%'"); 
        }
        //echo $collection->getSelect();
        return $this;
    }
    
    /*
     * Product name filter to the grid
     * @param object $collection
     * @param object $column
     * @return Clarion_ReviewReminder_Block_Adminhtml_Reviewreminder_Grid
     */
    protected function _productNameCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        
        $productName = $value;
        if(!empty($productName)){
            $collection->getSelect()
                ->where("pet.value='" . $productName."'"); 
        }
        //echo $collection->getSelect();
        return $this;
    }
    
    /**
     * Prepare reviewreminder grid collection object
     *
     * @return Clarion_ReviewReminder_Block_Adminhtml_Reviewreminder_Grid
     */
    protected function _prepareCollection()
    {
        //get order item collection
        $collection = Mage::getModel('sales/order_item')->getCollection();
        //get order table
        $orderTable = Mage::getSingleton('core/resource')->getTableName('sales/order');
        //get review reminder table
        $reviewReminderTable = Mage::getSingleton('core/resource')->getTableName('clarion_reviewreminder/reviewreminder');
        
        //join with sales_flat_order table
        $collection->getSelect()
            ->joinLeft(array('sfo' => $orderTable), 'main_table.order_id=sfo.entity_id', 
                array('sfo.entity_id', 'sfo.status', 'sfo.customer_firstname', 'sfo.customer_lastname', 'sfo.customer_id'));
        
        //Check is customer_id not null
        $collection->getSelect()
            ->where('sfo.customer_id IS NOT NULL');
        
        //Check is reminder already added
        $collection->getSelect()
            ->where('(sfo.customer_id, main_table.product_id) NOT IN (
                SELECT crr.customer_id, crr.product_id 
                FROM ' . $reviewReminderTable . ' AS crr)');
        
        //Check is review already added
        //get review table
        $reviewTable = Mage::getSingleton('core/resource')->getTableName('review/review');
        //get review detail table
        $reviewDetailTable = Mage::getSingleton('core/resource')->getTableName('review/review_detail');
        $collection->getSelect()
            ->where('(sfo.customer_id, main_table.product_id) NOT IN (
                SELECT rd.customer_id , r.entity_pk_value AS product_id
                FROM ' . $reviewTable . ' as r
                JOIN ' . $reviewDetailTable . ' as rd ON rd.review_id=r.review_id
                WHERE r.entity_id=1)
            ');
        
        
        /* @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $this->setCollection($collection);
        //echo $collection->getSelect();
        return parent::_prepareCollection();
    }
    
    /**
     * Prepare default grid column
     *
     * @return Clarion_ReviewReminder_Block_Adminhtml_Reviewreminder_Grid
     */
    protected function _prepareColumns()
    {
        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toArray();
        
        $this->addColumn('order_id', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Order #'),
            'sortable'=>true,
            'index'=>'entity_id',
             'width' => '100px',
        ));
        
        $this->addColumn('customer_name', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Customer Name'),
            'sortable'=>true,
            'index'=>'customer_firstname',
            'renderer' => 'clarion_reviewreminder/adminhtml_addReminder_renderer_customerName',
            'filter_condition_callback' => array($this, '_customerNameCondition')
        ));
        
        $this->addColumn('product_name', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Product Name'),
            'sortable'=>true,
            'index'=>'name',
        ));
        
        $this->addColumn('order_status', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Order Status'),
            'sortable'=>true,
            'index'=>'status',
            'type'  => 'options',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));
        
        $this->addColumn('order_date', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Order Date'),
            'sortable'=>true,
            'index'=>'created_at',
            'type'  => 'datetime',
        ));
        
        return $this;
    }
    
    /**
     * Mass Actions. 
     * 
     * These used basically to do operations on multiple rows together.
     */
    protected function _prepareMassaction()
    {
        /**
         * id is the database column that serves as the unique identifier
         */
        $this->setMassactionIdField('item_id');
        
        /**
         * By using this we can set name of checkbox, used for selection. Which 
         * is used to pass all the ids to the controller.
         */
        $this->getMassactionBlock()->setFormFieldName('itemIds');
        
        /**
         * url - sets url for the delete action
         * confirm - This shows the user a confirm dialog before submitting the URL
         */
        $this->getMassactionBlock()->addItem('addReminder', array(
            'label'    => Mage::helper('clarion_reviewreminder')->__('Add Reminder'),
            'url'      => $this->getUrl('*/*/massAddReminder'),
            'confirm'  => Mage::helper('clarion_reviewreminder')->__('Are you sure?')
        ));
        return $this;
    }
}