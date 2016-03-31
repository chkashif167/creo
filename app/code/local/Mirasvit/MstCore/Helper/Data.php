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



class Mirasvit_MstCore_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * @var array
     */
    protected $modules = array();

    /**
     * @param string $modulename
     * @return bool
     */
    public function isModuleInstalled($modulename)
    {
        if (isset($this->modules[$modulename])) {
            return $this->modules[$modulename];
        }

        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;

        if(isset($modulesArray[$modulename])
            && $modulesArray[$modulename]->is('active')
            && $modulesArray[$modulename]->is('codePool')) {
            $codePool = $modulesArray[$modulename]->codePool;
            $configFile = Mage::getBaseDir('code'). DS . $codePool . DS . str_replace('_', DS, $modulename) . DS . 'etc' . DS . 'config.xml';
            if (file_exists($configFile)) {
                $this->modules[$modulename] = true;
            }
        } else {
            $this->modules[$modulename] = false;
        }
        return $this->modules[$modulename];
    }

    public function pr($arr, $ip = false, $die = false)
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $clientIp = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $clientIp = $_SERVER['REMOTE_ADDR'];
        }

        if (!$ip) {
            pr($arr);
        } elseif ($clientIp == $ip) {
            pr($arr);
            if ($die) {
                die();
            }
        }
    }

    public function copyConfigData($oldPath, $newPath, $callbackFunction = false)
    {
        if ($oldPath == $newPath) {
            throw new Exception('Old path should now be equal to the new path. Otherwise, we will have possible data loses.');
        }
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');
        $query = "SELECT * FROM {$resource->getTableName('core/config_data')} where path='$oldPath'";
        $results = $connection->fetchAll($query);
        foreach ($results as $row) {
            $query = "REPLACE INTO {$resource->getTableName('core/config_data')} (scope, scope_id, path, value)
                VALUES (?, ?, ?, ?)";
            $value = $row['value'];
            if ($callbackFunction) {
                $value = call_user_func($callbackFunction, $value, $row['scope'], $row['scope_id']);
            }
            $connection->query($query, array($row['scope'], $row['scope_id'], $newPath, $value));
        }
    }

    /**
     * Return array of used cache systems: cache_name => cache_clear_method.
     *
     * @return array
     */
    public function getUsedCaches()
    {
        $caches = array(
            'APC' => 'apc_clear_cache',
            'OPcache' => 'opcache_reset',
            'xCache' => 'xcache_clear_cache',
        );

        foreach ($caches as $name => $method) {
            if (!function_exists($method)) {
                unset($caches[$name]);
            }
        }

        return $caches;
    }

    public function getModules()
    {
        if ($modules = Mage::app()->getRequest()->getParam('modules')) {
            if (strpos($modules, ',') !== false) {
                $modules = explode(',', $modules);
            } else {
                $modules = $this->getFolders($modules);
            }
        } else {
            $modules = array();
        }

        if (count($modules) == 0) {
            $mstdir = Mage::getBaseDir('app').DS.'code'.DS.'local'.DS.'Mirasvit';

            if ($handle = opendir($mstdir)) {
                while (false !== ($entry = readdir($handle))) {
                    if (substr($entry, 0, 1) != '.') {
                        if (!Mage::helper('mstcore')->isModuleInstalled("Mirasvit_$entry")) {
                            continue;
                        }
                        $modules[] = $entry;
                    }
                }
                closedir($handle);
            }
        }

        return $modules;
    }

    /**
     * @param string $sku
     *
     * @return array
     */
    protected function getFolders($sku)
    {
        switch ($sku) {
            case 'SSU':
                return array('SearchAutocomplete', 'SearchIndex', 'SearchLandingPage', 'SearchSphinx', 'Misspell');
            case 'RMA':
                return array('Rma');
            case 'HDMX':
                return array('Helpdesk');
            case 'SEO':
                return array('Seo', 'SeoAutolinks', 'SeoFilter', 'SeoSitemap');
            case 'SSC':
                return array('Misspell');
            case 'PO':
                return array('Action');
            case 'FAR':
                return array('AsyncIndex');
            case 'SSP':
                return array('SearchIndex', 'SearchLandingPage', 'SearchSphinx');
            case 'YME':
                return array('PriceExport');
            case 'SAS':
                return array('SearchAutocomplete');
            case 'CV':
                return array('CatalogVideo');
            case 'BMS':
                return array('Banner');
            case 'MMP':
                return array('Menu');
            case 'ACC':
                return array('AsyncCache');
            case 'SSM':
                return array('SeoSitemap');
            case 'SAL':
                return array('SeoAutolinks');
            case 'CLB':
                return array('CatalogLabel');
            case 'PFE':
                return array('FeedExport');
            case 'TES':
                return array('Email', 'EmailDesign', 'EmailReport', 'EmailSmtp');
            case 'FPC':
                return array('Fpc');
            case 'KB':
                return array('Kb');
            case 'PQ':
                return array('ProductQuestion');
            case 'RWP':
                return array('Rewards', 'RewardsSocial');
            case 'ADVN':
                return array('Advn', 'AdvnDesign', 'EmailSmtp');
            case 'ADVR':
                return array('Advd', 'Advr');
            case 'SCR':
                return array('Credit');
            case 'GRY':
                return array('Giftr');
        }

        return array();
    }
}

if (!function_exists('pr')) {
    function pr($arr)
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
}
