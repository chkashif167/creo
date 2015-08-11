<?php

class FacileCheckout_OnestepCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_agree = null;

    public function isOnestepCheckoutEnabled()
    {
        return (bool)Mage::getStoreConfig('onestepcheckout/general/enabled');
    }

    public function isGuestCheckoutAllowed()
    {
        return Mage::getStoreConfig('onestepcheckout/general/guest_checkout');
    }

    public function isShippingAddressAllowed()
    {
    	return Mage::getStoreConfig('onestepcheckout/general/shipping_address');
    }

    public function getAgreeIds()
    {
        if (is_null($this->_agree))
        {
            if (Mage::getStoreConfigFlag('onestepcheckout/agreements/enabled'))
            {
                $this->_agree = Mage::getModel('checkout/agreement')->getCollection()
                    												->addStoreFilter(Mage::app()->getStore()->getId())
                    												->addFieldToFilter('is_active', 1)
                    												->getAllIds();
            }
            else
            	$this->_agree = array();
        }
        return $this->_agree;
    }
    
    public function isSubscribeNewAllowed()
    {
        if (!Mage::getStoreConfig('onestepcheckout/general/newsletter_checkbox'))
            return false;

        $cust_sess = Mage::getSingleton('customer/session');
        if (!$cust_sess->isLoggedIn() && !Mage::getStoreConfig('newsletter/subscription/allow_guest_subscribe'))
            return false;

		$subscribed	= $this->getIsSubscribed();
		if($subscribed)
			return false;
		else
			return true;
    }
    
    public function getIsSubscribed()
    {
        $cust_sess = Mage::getSingleton('customer/session');
        if (!$cust_sess->isLoggedIn())
            return false;

        return Mage::getModel('newsletter/subscriber')->getCollection()
            										->useOnlySubscribed()
            										->addStoreFilter(Mage::app()->getStore()->getId())
            										->addFieldToFilter('subscriber_email', $cust_sess->getCustomer()->getEmail())
            										->getAllIds();
    }
    
    public function getOPCVersion()
    {
    	return (string) Mage::getConfig()->getNode()->modules->FacileCheckout_OnestepCheckout->version;
    }
    
    public function getMagentoVersion()
    {
		$ver_info = Mage::getVersionInfo();
		$mag_version	= "{$ver_info['major']}.{$ver_info['minor']}.{$ver_info['revision']}.{$ver_info['patch']}";
		
		return $mag_version;
    }  

    public function getNoItemsText()
    {
		$url	= 'http://www.cogzidel.com/magento/opclicense/nocartitemstext.php';

		$text	= file_get_contents($url);
		if(empty($text))
			$text = 'jVPfb5swEH7vX3FCm0ikAtrrRoi2bGrz0DVqUk19QsYcsYXBlm3K2F8/G5Iuk9K0jxzffT/Od8ssLfkzUEGMWQSK7DGy3AoMspR9ytKlYgqQMgkfLOMmyvJ8Fj7dPz7A9vZ+s1n/vIHV14cdrLfw426zewrnsMzSxLcmjje7OmWnRNsIG2WH4AzzHu0dGuMcmG9C0no2H2s3WnYKy1vbiNlIfq5xxbiYICFlSGvZ2dyr5aNa3vPSgcL5F9d/lapXYm2l1sM1+AIYJpXi7R48C7gC7bTG1ooBRsr4EFRd4Nt5oqMdMIOx2EBPDBQdFxaKAda/vgMBpWXlgnPZEgHh+YyeLyXANFaL4KMJwBLtIi2CvBCkrYOsxwJKNHzfQsV1kyYkg/AaQmat+pwkfd/HvLWoudQOOiFjKpvkgqBRSDkR/I8fBG/hogFcyaZBTREmK88opGrcyN5vBemBIqKyNZ2wTjea/kcnhBccA2nLcbylt2yktw9WSmFA8BovJ6gGXonB241P7E7V0Z/9nTDZGSvbd7z+RiAx6Faf0xoYanQ+/tPP3LfL6UJ2+LJvR/WT1T5AtgfEo3Z38E/+jWs48sZ+j2NLCoExqdzwp2OYjjRd/gU=';
		
		return $text;    	
    }
}