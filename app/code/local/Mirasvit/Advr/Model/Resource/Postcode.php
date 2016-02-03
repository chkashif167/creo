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


class Mirasvit_Advr_Model_Resource_Postcode extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('advr/postcode', 'postcode_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setData('postcode', preg_replace("/[^A-Z0-9]/", "", strtoupper($object->getData('postcode'))));

        $names = array('state', 'province', 'place', 'community');
        foreach ($names as $name) {
            $object->setData($name, Mage::helper('advr/geo')->formatName($object->getData($name)));
        }

        return parent::_beforeSave($object);
    }
}
