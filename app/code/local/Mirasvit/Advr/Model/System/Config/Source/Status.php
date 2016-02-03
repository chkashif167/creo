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



class Mirasvit_Advr_Model_System_Config_Source_Status extends Varien_Object
{
    public function toOptionArray()
    {
        $result = array();
        foreach (Mage::getSingleton('sales/order_config')->getStatuses() as $value => $label) {
            $result[] = array(
                'label' => $label,
                'value' => $value
            );
        }

        return $result;
    }

    public function toOptionHash()
    {
        $result = array();
        foreach (Mage::getSingleton('sales/order_config')->getStatuses() as $value => $label) {
            $result[$value] = $label;
        }

        return $result;
    }
}
