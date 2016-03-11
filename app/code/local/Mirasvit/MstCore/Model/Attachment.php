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
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_MstCore_Model_Attachment extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('mstcore/attachment');
    }

    public function getUrl()
    {
        $store = Mage::getModel('core/store')->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->getFirstItem();

        return Mage::getUrl('mstcore/attachment/download', array('uid' => $this->getUid(), '_store' => $store->getId()));
    }

    public function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->getUid()) {
            $uid = md5(
                Mage::getSingleton('core/date')->gmtDate().
                Mage::helper('mstcore/string')->generateRandHeavy(100));
            $this->setUid($uid);
        }
    }
}
