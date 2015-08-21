<?php

class Magestore_Magenotification_Block_Adminhtml_Notification_Inbox_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        $this->setSaveParametersInSession(true);
        $this->setId('magenotificationGrid');
        $this->setIdFieldName('notification_id');
        $this->setDefaultSort('added_date', 'desc');
        $this->setFilterVisibility(false);
    }

    /**
     * Init backups collection
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('magenotification/magenotification')
            ->getCollection()
			->addFieldToFilter('is_remove',0);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('severity', array(
            'header'    => Mage::helper('adminnotification')->__('Severity'),
            'width'     => '60px',
            'index'     => 'severity',
            'renderer'  => 'adminhtml/notification_grid_renderer_severity',
        ));

        $this->addColumn('added_date', array(
            'header'    => Mage::helper('adminnotification')->__('Date Added'),
            'index'     => 'added_date',
            'width'     => '150px',
            'type'      => 'datetime'
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('adminnotification')->__('Message'),
            'index'     => 'title',
            'renderer'  => 'adminhtml/notification_grid_renderer_notice',
        ));
		$this->addColumn('related_extensions', array(
            'header'    => Mage::helper('adminnotification')->__('Related Extensions'),
            'index'     => 'related_extensions',
			'renderer'  => 'magenotification/adminhtml_notification_inbox_grid_renderer_extensions',
        ));
        $this->addColumn('actions', array(
            'header'    => Mage::helper('adminnotification')->__('Actions'),
            'width'     => '250px',
            'sortable'  => false,
            'renderer'  => 'magenotification/adminhtml_notification_inbox_grid_renderer_actions',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('notification_id');
        $this->getMassactionBlock()->setFormFieldName('notification');

        $this->getMassactionBlock()->addItem('mark_as_read', array(
             'label'    => Mage::helper('adminnotification')->__('Mark as Read'),
             'url'      => $this->getUrl('*/*/massMarkAsRead', array('_current'=>true)),
        ));

        $this->getMassactionBlock()->addItem('remove', array(
             'label'    => Mage::helper('adminnotification')->__('Remove'),
             'url'      => $this->getUrl('*/*/massRemove'),
             'confirm'  => Mage::helper('adminnotification')->__('Are you sure?')
        ));

//        $this->getColumn('massaction')->setWidth('30px');

        return $this;
    }

    public function getRowClass(Varien_Object $row) {
        return $row->getIsRead() ? 'read' : 'unread';
    }

    public function getRowClickCallback()
    {
        return false;
    }
}
