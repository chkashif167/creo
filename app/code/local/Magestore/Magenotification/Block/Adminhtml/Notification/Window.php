<?php
class Magestore_Magenotification_Block_Adminhtml_Notification_Window extends Mage_Adminhtml_Block_Notification_Window{
	const XML_FREQUENCY_PATH    = 'magenotification/general/frequency';
	
	protected function _construct()
    {
        parent::_construct();
        $this->setHeaderText($this->escapeHtml($this->__('Incoming Message')));
        $this->setCloseText($this->escapeHtml($this->__('close')));
        $this->setReadDetailsText($this->escapeHtml($this->__('Read details')));
        $this->setNoticeText($this->escapeHtml($this->__('NOTICE')));
        $this->setMinorText($this->escapeHtml($this->__('MINOR')));
        $this->setMajorText($this->escapeHtml($this->__('MAJOR')));
        $this->setCriticalText($this->escapeHtml($this->__('CRITICAL')));
		$collectionNotifiByModule = $this->getNotificationByModule();
		if($collectionNotifiByModule &&  $collectionNotifiByModule->getFirstItem()->getData()){
			$inboxFirstData = $collectionNotifiByModule->getFirstItem()->getData();
			$this->setNoticeMessageText($this->escapeHtml($collectionNotifiByModule->getFirstItem()->getTitle()));
			$this->setNoticeMessageUrl($this->escapeUrl($collectionNotifiByModule->getFirstItem()->getUrl()));
		}else{
			$this->setNoticeMessageText($this->escapeHtml($this->getLastNotice()->getTitle()));
			$this->setNoticeMessageUrl($this->escapeUrl($this->getLastNotice()->getUrl()));
		}
        switch ($this->getLastNotice()->getSeverity()) {
            default:
            case Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE:
                $severity = 'SEVERITY_NOTICE';
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR:
                $severity = 'SEVERITY_MINOR';
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR:
                $severity = 'SEVERITY_MAJOR';
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL:
                $severity = 'SEVERITY_CRITICAL';
                break;
        }
        $this->setNoticeSeverity($severity);
    }
	
	public function canShow()
    {
		/* --- Start By Zuongthao ----*/
		$pathXmlModule = $this->getRequest()->getRouteName();
		$module = (string)Mage::getConfig()->getNode('admin/routers/'.$pathXmlModule.'/args/module');
		if(strpos($module ,'Magestore_') !== false){
			$moduleName = strtolower(str_replace('Magestore_','',$module));		
			$collection = $this->getNotificationByModule();	
			if($collection){
				$inboxFirstData = $collection->getFirstItem()->getData();
				if($inboxFirstData && Mage::getModel('core/cookie')->get('notification_'.$moduleName) != 1){
					$getFrequency =  Mage::getStoreConfig(self::XML_FREQUENCY_PATH) * 3600;
					$cookie = Mage::getSingleton('core/cookie');
					$cookie->set('notification_'.$moduleName, '1' ,$getFrequency,'/');
					$listPopCookie = $cookie->get('listPopCookie');
					if($listPopCookie ){
						$listPopCookie  = $listPopCookie.','.$inboxFirstData['magenotification_id'];
					}else{
						$listPopCookie  = $inboxFirstData['magenotification_id'];
					}
					$cookie->set('listPopCookie',$listPopCookie,$getFrequency);
					return true;
				}
			}
		}
		/* ---- End Zuongthao ---- */
		
        if (!is_null($this->_available)) {
            return $this->_available;
        }
        if (!Mage::getSingleton('admin/session')->isFirstPageAfterLogin()) {
            $this->_available = false;
            return false;
        }
        if (!$this->isOutputEnabled('Mage_AdminNotification')) {
            $this->_available = false;
            return false;
        }
        if (!$this->_isAllowed()) {
            $this->_available = false;
            return false;
        }
        if (is_null($this->_available)) {
            $this->_available = $this->isShow();
        }
        return $this->_available;
    }
	public function getNotificationByModule(){
		/* --- Start By Zuongthao ----*/
		$pathXmlModule = $this->getRequest()->getRouteName();
		$module = (string)Mage::getConfig()->getNode('admin/routers/'.$pathXmlModule.'/args/module');
		if(strpos($module ,'Magestore_') !== false){
			$moduleName = strtolower(str_replace('Magestore_','',$module));
			$inboxModel = Mage::getModel('magenotification/magenotification');
			$collection = $inboxModel->getCollection()
				->addFieldToFilter('is_read',0)
				->addFieldToFilter('is_remove',0)
				->addFieldToFilter('related_extensions', array(array('like' => '%,'.$moduleName),
																	array('like' => '%,'.$moduleName.',%'),
																	array('like' => $moduleName),
																	array('like' => $moduleName.',%'),
																	array('like' => '%0%'))); 
			$cookie = Mage::getSingleton('core/cookie');
			$listPopCookie = trim($cookie->get('listPopCookie'));
			$explodeListPopCookie = explode(',',$listPopCookie);	
			if(count($explodeListPopCookie) > 0 && $listPopCookie != ''){
				$collection->addFieldToFilter('magenotification_id',array('nin' => $explodeListPopCookie));
				}
			$collection->setOrder('added_date', 'desc');	
			return $collection;		
		}
		return null;
		/* ---- End Zuongthao ---- */		
	}


}