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


class Mirasvit_FeedExport_Model_Resource_Template extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('feedexport/template', 'template_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        Mage::helper('feedexport/format')->preparePostData($object);

        return parent::_beforeSave($object);
    }

    protected function _afterLoad (Mage_Core_Model_Abstract $object)
    {
        Mage::helper('feedexport/format')->expandFormat($object);

        return parent::_afterLoad($object);
    }
}