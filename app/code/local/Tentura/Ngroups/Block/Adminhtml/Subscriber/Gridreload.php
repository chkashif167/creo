<?php

class Tentura_Ngroups_Block_Adminhtml_Subscriber_Gridreload extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Constructor
     *
     * Set main configuration of grid
     */
    public function __construct() {
        parent::__construct();

        $this->setId('subscriberGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('subscriber_id', 'desc');

        $this->setTemplate('ngroups/customers_reload.phtml');
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {

        if ($id = $this->getRequest()->getParam('id')) {

            $groupSubscribers = Mage::getModel('ngroups/ngroups')->getGroupSubscribers($id);
            $list = Mage::helper("ngroups")->getSubscribersAsArray($groupSubscribers);
            $customers = array();
            $customers['nin'] = $list;
            $collection = Mage::getResourceSingleton('newsletter/subscriber_collection')->addFieldToFilter('subscriber_id', $customers);
            
        } else {
            $collection = Mage::getResourceSingleton('newsletter/subscriber_collection');
        }


//        $collection = Mage::getResourceSingleton('newsletter/subscriber_collection');
        /* @var $collection Mage_Newsletter_Model_Mysql4_Subscriber_Collection */
        $collection
                ->showCustomerInfo(true)
                ->addSubscriberTypeField()
                ->showStoreInfo();

        if ($this->getRequest()->getParam('queue', false)) {
            $collection->useQueue(Mage::getModel('newsletter/queue')
                            ->load($this->getRequest()->getParam('queue')));
        }
        
        $customers = Mage::getModel('customer/customer');
        $collection->getSelect()->joinLeft( array('c'=> Mage::getConfig()->getTablePrefix().'customer_entity'), 'c.entity_id = main_table.customer_id', array("gid"=>"group_id"));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('subscriber_id', array(
            'header' => Mage::helper('newsletter')->__('ID'),
            'index' => 'subscriber_id'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('newsletter')->__('Email'),
            'index' => 'subscriber_email'
        ));

        $this->addColumn('type', array(
            'header' => Mage::helper('newsletter')->__('Type'),
            'index' => 'type',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('newsletter')->__('Guest'),
                2 => Mage::helper('newsletter')->__('Customer')
            )
        ));

        $this->addColumn('gid', array(
            'header'    => Mage::helper('newsletter')->__('Customer Group'),
            'index'     => 'gid',
            'type'      => 'options',
            'options'   => Mage::helper("ngroups")->getGroupsList(),
            'filter_index' => "c.group_id"
        ));

        $this->addColumn('firstname', array(
            'header' => Mage::helper('newsletter')->__('Customer Firstname'),
            'index' => 'customer_firstname',
            'default' => '----'
        ));

        $this->addColumn('lastname', array(
            'header' => Mage::helper('newsletter')->__('Customer Lastname'),
            'index' => 'customer_lastname',
            'default' => '----'
        ));

                $this->addColumn('custom_subscriber_name', array(
                'header'    => Mage::helper('newsletter')->__('Customer Name'),
                'index'     => 'custom_subscriber_name',
                'default'   =>    '----'
        ));

        $this->addColumn('custom_subscriber_telephone', array(
                'header'    => Mage::helper('newsletter')->__('Customer Phone'),
                'index'     => 'custom_subscriber_telephone',
                'default'   =>    '----'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('newsletter')->__('Status'),
            'index' => 'subscriber_status',
            'type' => 'options',
            'options' => array(
                Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE => Mage::helper('newsletter')->__('Not activated'),
                Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED => Mage::helper('newsletter')->__('Subscribed'),
                Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED => Mage::helper('newsletter')->__('Unsubscribed'),
            )
        ));

        $this->addColumn('website', array(
            'header' => Mage::helper('newsletter')->__('Website'),
            'index' => 'website_id',
            'type' => 'options',
            'options' => $this->_getWebsiteOptions()
        ));

        $this->addColumn('group', array(
            'header' => Mage::helper('newsletter')->__('Store'),
            'index' => 'group_id',
            'type' => 'options',
            'options' => $this->_getStoreGroupOptions()
        ));

        $this->addColumn('store', array(
            'header' => Mage::helper('newsletter')->__('Store View'),
            'index' => 'store_id',
            'type' => 'options',
            'options' => $this->_getStoreOptions()
        ));

        return parent::_prepareColumns();
    }

    /**
     * Convert OptionsValue array to Options array
     *
     * @param array $optionsArray
     * @return array
     */
    protected function _getOptions($optionsArray) {
        $options = array();
        foreach ($optionsArray as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * Retrieve Website Options array
     *
     * @return array
     */
    protected function _getWebsiteOptions() {
        return Mage::getModel('adminhtml/system_store')->getWebsiteOptionHash();
    }

    /**
     * Retrieve Store Group Options array
     *
     * @return array
     */
    protected function _getStoreGroupOptions() {
        return Mage::getModel('adminhtml/system_store')->getStoreGroupOptionHash();
    }

    /**
     * Retrieve Store Options array
     *
     * @return array
     */
    protected function _getStoreOptions() {
        return Mage::getModel('adminhtml/system_store')->getStoreOptionHash();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('subscriber_id');
        $this->getMassactionBlock()->setFormFieldName('subscriber');

        $this->getMassactionBlock()->addItem('unsubscribe', array(
            'label' => Mage::helper('newsletter')->__(''),
            'url' => $this->getUrl('*/*/massUnsubscribe'),
            'onclick' => 'alert("a")',
        ));

        return $this;
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid/', array('_current' => true));

        //return $this->getUrl('adminhtml/newsletter_subscriber/index/', array('_current'=>true));
    }

}
