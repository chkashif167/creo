<?php
class Magestore_Magenotification_Block_Rewrite_AdminhtmlNotificationWindow extends Mage_Adminhtml_Block_Notification_Window
{
    protected function _construct()
    {
        parent::_construct();

		if(Mage::getModel('magenotification/magenotification')->is_existedUrl($this->getLastNotice()->getUrl()))
		{
			$url = $this->getUrl('magenotification/adminhtml_magenotification/readdetail',array('id'=>$this->getLastNotice()->getId()));
			
			$this->setHeaderText(addslashes($this->__('Magestore Message')));
	
			$this->setNoticeMessageUrl(addslashes($url));
		}
	}

}