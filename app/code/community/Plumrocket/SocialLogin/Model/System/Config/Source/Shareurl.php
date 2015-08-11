<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_SocialLogin
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


class Plumrocket_SocialLogin_Model_System_Config_Source_ShareUrl
{

    protected $_options = null;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_getOptions();
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = array();
        foreach ($this->_getOptions() as $option) {
            $options[ $option['value'] ] = $option['label'];
        }

        return $options;
    }

    protected function _getOptions()
    {
        if(is_null($this->_options)) {
            $invitationsEnabled = Mage::helper('pslogin')->moduleInvitationsEnabled();

            $options = array(
                array('value' => '__custom__', 'label' => Mage::helper('pslogin')->__('Redirect to Custom URL') ),
                array('value' => '__invitations'. (!$invitationsEnabled? 'off' : '') .'__', 'label' => Mage::helper('pslogin')->__('Plumrocket Invitations Promo Page'. (!$invitationsEnabled? ' (Not installed)' : '')) ),
                array('value' => '__none__', 'label' => Mage::helper('pslogin')->__('---') ),
            );

            $items = Mage::getSingleton('cms/page')->getCollection()->getItems();
            foreach ($items as $item) {
                if($item->getId() == 1) continue;
                $options[] = array('value' => $item->getId(), 'label' => $item->getTitle());
            }

            $this->_options = $options;
        }

        return $this->_options;
    }

}