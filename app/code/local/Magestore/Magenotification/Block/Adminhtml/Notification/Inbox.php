<?php

class Magestore_Magenotification_Block_Adminhtml_Notification_Inbox extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_notification_inbox';
		$this->_blockGroup = 'magenotification';
        $this->_headerText = Mage::helper('magenotification')->__('Messages Inbox');
        parent::__construct();
    }

    protected function _prepareLayout()
    {
        $this->_removeButton('add');
        return parent::_prepareLayout();
    }
}
