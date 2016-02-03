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


class Mirasvit_EmailDesign_Helper_Variables_Date
{
    public function getFormatedDate($parent, $args)
    {
        if (isset($args[0])) {
            $date = $args[0];
            $format = 'medium';

            if (isset($args[1])) {
                $format = $args[1];
            }

            $formatType = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT;
            
            switch ($format) {
                case 'full':
                    $formatType = Mage_Core_Model_Locale::FORMAT_TYPE_FULL;
                    break;
                case 'long':
                    $formatType = Mage_Core_Model_Locale::FORMAT_TYPE_LONG;
                    break;
                case 'medium':
                    $formatType = Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM;
                    break;
                case 'short':
                    $formatType = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT;
                    break;
            }

            return Mage::helper('core')->formatDate($date, $formatType);
        }  
    }
}
?>