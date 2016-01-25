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
 * @build     616
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_MstCore_Block_Adminhtml_Validator extends Mage_Adminhtml_Block_Template
{
    public function _prepareLayout()
    {
        $this->setTemplate('mstcore/validator.phtml');
    }

    /**
     * Get test results
     *
     * @param  string $testType 'test' - for common tests, 'dev' - for dev tests
     * @return array
     */
    public function getResults($testType = 'test')
    {
        $results = array();

        $modules = Mage::helper('mstcore')->getModules();

        foreach ($modules as $module) {
            $helper = $this->getValidatorHelper($module);
            if ($helper) {
                $results += $helper->runTests($testType);
            }
        }

        return $results;

    }

    public function getValidatorHelper($module)
    {
        if ($module == 'SEO') {
            $module = 'Seo';
        } elseif ($module == 'MCore') {
            $module = 'MstCore';
        }

        $file = Mage::getBaseDir().'/app/code/local/Mirasvit/'.$module.'/Helper/Validator.php';

        if (file_exists($file)) {
            $helper = Mage::helper(strtolower($module).'/Validator');
            return $helper;
        }
    }

    public function getBackUrl()
    {
        return Mage::helper('core/http')->getHttpReferer();
    }

    public function getClearCacheUrl()
    {
        return Mage::helper('adminhtml')->getUrl('*/*/clearCache');
    }

    public function isUsedExternalCache()
    {
        return (bool) count(Mage::helper('mstcore')->getUsedCaches()) > 0;
    }
}
