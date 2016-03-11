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



class Mirasvit_MstCore_Block_Adminhtml_Validator extends Mage_Adminhtml_Block_Template
{
    public function _prepareLayout()
    {
        $this->setTemplate('mstcore/validator.phtml');
    }

    protected $results = array();
    /**
     * Get test results.
     *
     * @param string $testType 'test' - for common tests, 'dev' - for dev tests
     *
     * @return array
     */
    public function getResults($testType = 'test')
    {
        if (!isset($this->results[$testType])) {
            $results = array();

            $modules = Mage::helper('mstcore')->getModules();

            foreach ($modules as $module) {
                $helper = $this->getValidatorHelper($module);
                if ($helper) {
                    $results += $helper->runTests($testType);
                }
            }
            $this->results[$testType] = $results;
        }

        return $this->results[$testType];
    }

    /**
     * @return bool
     */
    public function getIsPassed()
    {
        $results = $this->getResults();
        foreach ($results as $result) {
            if ($result[0] == Mirasvit_MstCore_Helper_Validator_Abstract::FAILED) {
                return false;
            }
        }

        return true;
    }

    public function getValidatorHelper($module)
    {
        if ($module == 'SEO') {
            $module = 'Seo';
        } elseif ($module == 'MCore') {
            $module = 'MstCore';
        } elseif ($module == 'RMA') {
            $module = 'Rma';
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
