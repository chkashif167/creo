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



class Mirasvit_MstCore_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function devTestCache()
    {
        $result = self::SUCCESS;
        $title = 'Core: PHP Cache';
        $description = array();

        foreach (Mage::helper('mstcore')->getUsedCaches() as $name => $method) {
            $description[] = "PHP Cache $name is installed. Please, clear it after installation or upgrade (method $method)".
            ' <a style="cursor:pointer" href="'.Mage::helper('adminhtml')->getUrl('adminhtml/mstcore_validator/clearCache', array('cache_method' => $method)).'">Flush Cache</a>';
            $result = self::INFO;
        }

        return array($result, $title, $description);
    }

    public function devTestRedisCache()
    {
        $result = self::SUCCESS;
        $title = 'Core: Redis Cache';
        $description = array();

        $backend = (string) Mage::getConfig()->getNode('global/cache/backend');
        if ($backend == 'Cm_Cache_Backend_Redis') {
            $description[] = 'Cm_Cache_Backend_Redis is installed. Please, clear it after installation or upgrade. To clear cache use SSH:<br>
<pre>
$ redis-cli
redis> flushall
</pre>
            ';
            $result = self::INFO;
        }

        return array($result, $title, $description);
    }

    public function testMirasvitMstCoreCrc()
    {
        $modules = array('MCore');

        return Mage::helper('mstcore/validator_crc')->testMirasvitCrc($modules);
    }

    public function devTestCompilation()
    {
        $result = self::SUCCESS;
        $title = 'Core: Compilation';
        $description = array();
        $compiler = Mage::getModel('compiler/process');

        if (defined('COMPILER_INCLUDE_PATH')) {
            $result = self::INFO;
            $description[] = 'Compilation status: Enabled, <a href="'.Mage::helper('adminhtml')->getUrl('adminhtml/mstcore_validator/compiler', array('action' => 'disable')).'">Disable</a>';
        }

        return array($result, $title, $description);
    }

    public function devTestCDN()
    {
        $result = self::SUCCESS;
        $title = 'Core: CDN';
        $description = array();

        $host = Mage::getModel('core/url')->parseUrl(Mage::getBaseUrl())->getHost();
        $urlTypes = array(
            Mage_Core_Model_Store::URL_TYPE_MEDIA,
            Mage_Core_Model_Store::URL_TYPE_SKIN,
            Mage_Core_Model_Store::URL_TYPE_JS,
        );

        foreach ($urlTypes as $type) {
            $parsedUrl = parse_url(Mage::getBaseUrl($type));
            if ($host != $parsedUrl['host']) {
                $description[] = 'CDN is used for the Base '.ucfirst($type).' URL. Base host: "'.$host.'". CDN host: "'.$parsedUrl['host'].'"';
                $urlTypes[$type] = $parsedUrl['host'];
            }
        }

        if (count($description)) {
            $result = self::INFO;
            $description[] = 'These settings applied at the <a href="'.Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/', array('section' => 'web')).'">Web Section</a>.';
        }

        return array($result, $title, $description);
    }

    public function devTestMerge()
    {
        $result = self::SUCCESS;
        $title = 'Core: Merge js/css';
        $description = array();

        if (Mage::getStoreConfig('dev/js/merge_files')) {
            $description[] = 'JS merge is enabled.';
            $result = self::INFO;
        }

        if (Mage::getStoreConfig('dev/css/merge_css_files')) {
            $description[] = 'CSS merge is enabled.';
            $result = self::INFO;
        }

        if (count($description)) {
            $result = self::INFO;
            $description[] = 'To disable merging navigate to the <a href="'.Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/', array('section' => 'dev')).'">Developer Section</a>.';
        }

        return array($result, $title, $description);
    }

    public function devTestMstExtensionStatus()
    {
        $result = self::SUCCESS;
        $title = 'Extension Status';
        $description = array();

        $modules = Mage::helper('mstcore')->getModules();

        foreach ($modules as $module) {
            if (!Mage::helper('mstcore')->isModuleOutputEnabled('Mirasvit_'.$module)) {
                $description[] = "The extension 'Mirasvit {$module}' is disabled from admin panel.";
            }
        }

        if (count($description) > 0) {
            $result = self::INFO;
            $description[] = "Go to <a href='".Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/', array('section' => 'advanced'))."'>Advanced Settings</a> to enable the extension.";
        }

        return array($result, $title, $description);
    }

    public function devTestCron()
    {
        $result = self::SUCCESS;
        $title = 'Cron Job';
        $description = array();

        $cronStatus = Mage::helper('mstcore/cron')->checkCronStatus(false, false, '');

        if ($cronStatus !== true) {
            $description[] = $cronStatus;
            $result = self::WARNING;
        }

        return array($result, $title, $description);
    }

    public function devReadChangelog()
    {
        $result = self::SUCCESS;
        $title = 'Changelog';
        $description = array();

        $modules = Mage::helper('mstcore')->getModules();

        foreach ($modules as $module) {
            $changelogPath = Mage::getBaseDir('app').DS.'code'.DS.'local'.DS.'Mirasvit'.DS.$module.DS.'changelog.txt';
            if (file_exists($changelogPath)) {
                $description[] = '<b>'.$module.'</b>:<br>'.nl2br(file_get_contents($changelogPath));
                $result = self::INFO;
            }
        }

        return array($result, $title, $description);
    }
}
