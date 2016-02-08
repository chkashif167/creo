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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Email_Model_System_Source_CrossSell
{
    const MAGE_CROSS   = 'mage_cross';
    const MAGE_RELATED = 'mage_related';
    const MAGE_UPSELLS = 'mage_upsells';
    const AW_WBTAB     = 'AW_WBTAB';
    const AW_ARP2      = 'AW_ARP2';
    const TM_CUSTOMER  = 'TM_CUSTOMER';
    const TM_ORDER     = 'TM_ORDER';
    const AMASTY_MV    = 'AMASTY_MV';

    public static function toArray()
    {
        $result = array(
            self::MAGE_CROSS   => Mage::helper('email')->__('Cross-sell products'),
            self::MAGE_RELATED => Mage::helper('email')->__('Related products'),
            self::MAGE_UPSELLS => Mage::helper('email')->__('Upsell products'),
            self::AW_WBTAB     => Mage::helper('email')->__('AheadWorks Who bought this also bought'),
            self::AW_ARP2      => Mage::helper('email')->__('AheadWorks Autorelated products 2'),
            self::TM_CUSTOMER  => Mage::helper('email')->__('TM Sold Together - Customers Who Bought This Item Also Bought'),
            self::TM_ORDER     => Mage::helper('email')->__('TM Sold Together - Frequently Bought Together'),
            self::AMASTY_MV    => Mage::helper('email')->__('Amasty Most Viewed'),
        );

        return $result;
    }

    public static function toOptionArray()
    {
        $options = self::toArray();
        $result  = array();

        foreach ($options as $key => $value)
            $result[] = array(
                'value' => $key,
                'label' => $value
            );

        return $result;
    }
}