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


class Mirasvit_EmailDesign_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function testPermissions()
    {
        $result = self::SUCCESS;
        $title  = 'Follow Up Email (Design): Correct permissions on folders';
        $description = array();

        $pathes   = array(
            Mage::getBaseDir('media') . '/',
            Mage::getBaseDir('media') . DS . 'emaildesign/',
        );

        foreach ($pathes as $path) {
            if (!$this->ioIsWritable($path)) {
                $description[] = "Path '$path' is not writable";
                $result = self::FAILED;
            }
        }

        return array($result, $title, $description);
    }
} 