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
 * @version   1.1.2
 * @build     671
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Model_System_Config_Source_Archive
{
    public function toOptionArray($empty = false)
    {        
        if ($empty) {
            $result[] = array(
                'label' => Mage::helper('feedexport')->__('Disabled'),
                'value' => ''
            );
        }

        $result[] = array(
            'label' => Mage::helper('feedexport')->__('ZIP (*.zip)'),
            'value' => 'zip'
        );
        // $result[] = array(
        //     'label' => Mage::helper('feedexport')->__('GZ (*.gz)'),
        //     'value' => 'gz'
        // );

        return $result;
    }
    
}