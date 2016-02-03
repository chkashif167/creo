<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Advd_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Template
{
    protected $storeSwitcher = null;

    protected function _prepareLayout()
    {
        $this->setTemplate('mst_advd/dashboard.phtml');

        $this->_initStoreSwitcher();

        return parent::_prepareLayout();
    }

    protected function _initStoreSwitcher()
    {
        $this->storeSwitcher = Mage::app()->getLayout()->createBlock('adminhtml/store_switcher')
            ->setTemplate('mst_advd/store_switcher.phtml')
            ->setStoreVarName('store_ids');

        return $this;
    }

    public function getStoreSwitcher()
    {
        return $this->storeSwitcher;
    }

    public function getDashboard()
    {
        return Mage::registry('current_dashboard');
    }

    public function getNotificationEditUrl()
    {
        $notification = Mage::getSingleton('advd/notification')->loadForCurrentUser();

        return Mage::helper('adminhtml')->getUrl(
            'adminhtml/advd_notification/edit',
            array('id' => $notification->getId())
        );
    }
}
