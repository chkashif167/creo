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


class Mirasvit_EmailSmtp_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function testRewrites()
    {
        $result = self::SUCCESS;
        $title = 'Follow Up Email (Smtp): Check Rewrites';
        $description = array();

        $validateRewrite = $this->validateRewrite('emailsmtp/email_template', 'Mirasvit_EmailSmtp_Model_Email_Template');
        if ($validateRewrite !== true) {
            $result = self::FAILED;
            $description = $validateRewrite;
        }

        return array($result, $title, $description);
    }

    public function testAnotherExtensions()
    {
        $result = self::SUCCESS;
        $title = 'Follow Up Email (Smtp): Conflicts with similar extensions';
        $description = array();

        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());

        foreach ($modules as $module) {
            if (stripos($module, 'smtp') !== false && $module != 'Mirasvit_EmailSmtp') {
                $result = self::FAILED;
                $description[] = "Another SMTP extension '$module' installed.";
            }
        }

        return array($result, $title, $description);
    }
}