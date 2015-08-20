<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    28th Nov, 2014
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    Manage review reminder grid block 
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Clarion_ReviewReminder_Block_Adminhtml_Reviewreminder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reviewreminderGrid');
        $this->setDefaultSort('reminder_id');
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
                ->where("cust_ent1.value like '%" . $customerFirstName."%'"); 
        }
        
        if(!empty($customerLastName)){
            $collection->getSelect()
                ->where("cust_ent2.value like '%" . $customerLastName."%'"); 
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
        $collection = Mage::getModel('clarion_reviewreminder/reviewreminder')->getCollection()
                ->addCustomerNameToSelect()
                ->addProductNameToSelect()
                ->addOrderStatusAndDateToSelect()
                ->addFieldToFilter('is_review_added', 0);
        
        /* @var $collection Clarion_ReviewReminder_Model_Resource_Reviewreminder_Collection */
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
        
        $this->addColumn('reminder_id', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Reminder Id'),
            'sortable'=>true,
            'index'=>'reminder_id',
            'width' => '100px',
        ));
        
        $this->addColumn('order_id', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Order #'),
            'sortable'=>true,
            'index'=>'order_id',
            'width' => '100px',
        ));
        
        $this->addColumn('customer_name', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Customer Name'),
            'sortable'=>true,
            'index'=>'customer_name',
            'filter_condition_callback' => array($this, '_customerNameCondition')
        ));
        
        $this->addColumn('product_name', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Product Name'),
            'sortable'=>true,
            'index'=>'product_name',
            'filter_condition_callback' => array($this, '_productNameCondition')
        ));
        
        $this->addColumn('order_status', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Order Status'),
            'sortable'=>true,
            'index'=>'order_status',
            'type'  => 'options',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            'filter_condition_callback' => array($this, '_orderStausCondition'),
            'width' => '100px',
        ));
        
        $this->addColumn('order_date', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Order Date'),
            'sortable'=>true,
            'index'=>'order_date',
            'type'  => 'date',
            'filter_condition_callback' => array($this, '_orderDateCondition'),
            'width'     => '120px',
        ));
         
        $this->addColumn('is_reminder_sent', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Reminder Sent'),
            'sortable'=>true,
            'index'=>'is_reminder_sent',
            'type' => 'options',
            'options' => $yesno,
            'width' => '100px',
        ));
        
        $this->addColumn('sent_at', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Sent At'),
            'sortable'=>true,
            'index'=>'sent_at',
            'type' => 'datetime',
            'width' => '100px',
        ));
        
        $this->addColumn('reminder_count', array(
            'header'=>Mage::helper('clarion_reviewreminder')->__('Reminder #'),
            'sortable'=>true,
            'index'=>'reminder_count',
            'width' => '100px',
        ));
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
        $this->setMassactionIdField('reminder_id');
        
        /**
         * By using this we can set name of checkbox, used for selection. Which 
         * is used to pass all the ids to the controller.
         */
        $this->getMassactionBlock()->setFormFieldName('reminderIds');
        
        /**
         * url - sets url for the delete action
         * confirm - This shows the user a confirm dialog before submitting the URL
         */
        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('clarion_reviewreminder')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('clarion_reviewreminder')->__('Are you sure?')
        ));
        
        //Send reminder mass action
        $this->getMassactionBlock()->addItem('Send reminder', array(
            'label'    => Mage::helper('clarion_reviewreminder')->__('Send reminder'),
            'url'      => $this->getUrl('*/*/massSendReminder'),
            'confirm'  => Mage::helper('clarion_reviewreminder')->__('Are you sure?')
        ));
        
        return $this;
    }
    
    /*
     * Order status filter to the grid
     * @param object $collection
     * @param object $column
     * @return Clarion_ReviewReminder_Block_Adminhtml_Reviewreminder_Grid
     */
    protected function _orderStausCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        
        $orderStatus = $value;
        if(!empty($orderStatus)){
            $collection->getSelect()
                ->where("sot.status='" . $orderStatus."'"); 
        }
        //echo $collection->getSelect();
        return $this;
    }
    
     /*
     * Order date filter to the grid
     * @param object $collection
     * @param object $column
     * @return Clarion_ReviewReminder_Block_Adminhtml_Reviewreminder_Grid
     */
    protected function _orderDateCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        
        $orderDate = $value;
        
        $fromDate = isset($orderDate['from']) ? (is_object($orderDate['from']) ? date('Y-m-d', $orderDate['from']->get()) :'' ) : '';
        $toDate = isset($orderDate['to']) ? (is_object($orderDate['to']) ? date('Y-m-d', $orderDate['to']->get()) :'' ) : '';
        
        if(!empty($fromDate) && !empty($toDate)){
           $collection->getSelect()
            ->where("DATE(sot.created_at) BETWEEN '".$fromDate."' AND '".$toDate."' "); 
        }
        //echo $collection->getSelect();
        return $this;
    }
}