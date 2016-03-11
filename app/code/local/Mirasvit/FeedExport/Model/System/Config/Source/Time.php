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


class Mirasvit_FeedExport_Model_System_Config_Source_Time
{
        
    public function toOptionArray()
    {        
        $result = array();

        for ($i = 0; $i < 24; $i++) {
            $hour = $i;
            $suffix = ' AM';
            if ($hour > 12) {
                $hour -= 12;
                $suffix = ' PM';
            } 

            if ($hour < 10) {
                $hour = '0'.$hour;
            }

            $result[] = array(
                'label' => $hour.':00'.$suffix,
                'value' => $i * 60,
            );
            $result[] = array(
                'label' => $hour.':30'.$suffix,
                'value' => $i * 60 + 30,
            );
        }

        return $result;
    }
}