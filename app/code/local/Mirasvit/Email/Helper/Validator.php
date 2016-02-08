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



class Mirasvit_Email_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function testCustomerLogging()
    {
        $result = self::SUCCESS;
        $title = 'Follow Up Email (Email): Customer Logging';
        $description = array();

        if (@class_exists('Mage_Log_Model_Adminhtml_System_Config_Source_Loglevel')) {
            $configValue = Mage::getStoreConfig('system/log/enable_log');
            if ($configValue != Mage_Log_Model_Adminhtml_System_Config_Source_Loglevel::LOG_LEVEL_ALL) {
                $result = self::FAILED;
                $description[] = 'Customer Logging: Disabled. Events "Customer Activity" and "Customer Logged In" cannot be tracked by extension.';
                $description[] = 'To enable customer logging navigate to the <a href="'.Mage::helper('adminhtml')->getUrl('*/system_config/edit/', array('section' => 'system')).'">Log Section</a> and set the setting "Enable Log" to "Yes".';
            }
        }

        return array($result, $title, $description);
    }
}
