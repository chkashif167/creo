<?php
class Magestore_Magenotification_Block_Rewrite_AdminhtmlNotificationGridRendererActions 
		extends Mage_Adminhtml_Block_Notification_Grid_Renderer_Actions
{
   public function render(Varien_Object $row)
    {
        
		if(! Mage::getModel('magenotification/magenotification')->is_existedUrl($row->getUrl()))
		{
			return parent::render($row);
		}
		
		$read_url = $this->getUrl('magenotification/adminhtml_magenotification/readdetail',array('id'=>$row->getId()));
			
		if (!$row->getIsRead()) {
            return sprintf('<a target="_blank" href="%s">%s</a> | <a href="%s">%s</a> | <a href="%s" onClick="deleteConfirm(\'%s\',this.href); return false;">%s</a>',
                $read_url,
                Mage::helper('adminnotification')->__('Read Details'),
                $this->getUrl('*/*/markAsRead/', array('_current'=>true, 'id' => $row->getId())),
                Mage::helper('adminnotification')->__('Mark as Read'),
                $this->getUrl('*/*/remove/', array('_current'=>true, 'id' => $row->getId())),
                Mage::helper('adminnotification')->__('Are you sure?'),
                Mage::helper('adminnotification')->__('Remove')
            );
        }
        else {
            return sprintf('<a target="_blank" href="%s">%s</a> | <a href="%s" onClick="deleteConfirm(\'%s\',this.href); return false;">%s</a>',
                $read_url,
                Mage::helper('adminnotification')->__('Read Details'),
                $this->getUrl('*/*/remove/', array('_current'=>true, 'id' => $row->getId())),
                Mage::helper('adminnotification')->__('Are you sure?'),
                Mage::helper('adminnotification')->__('Remove')
            );
        }
    }

}