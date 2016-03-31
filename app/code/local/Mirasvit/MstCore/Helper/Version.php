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


class Mirasvit_MstCore_Helper_Version extends Mage_Core_Helper_Abstract
{
    protected static $_edition = false;

    public static function getEdition()
    {
        if (!self::$_edition) {
            $configEE = BP."/app/code/core/Enterprise/Enterprise/etc/config.xml";
            if (!file_exists($configEE)) {
                self::$_edition = 'ce';
            } else {
                $xml = @simplexml_load_file($configEE,'SimpleXMLElement', LIBXML_NOCDATA);
                if ($xml !== false) {
                    $package = (string)$xml->default->design->package->name;
                    if (!$package) {
                        $package = strtolower(Mage::getEdition());
                    }
                    if ($package == 'enterprise') {
                        self::$_edition = 'ee';
                    } else {
                        self::$_edition = 'pe';
                    }
                } else {
                    self::$_edition = 'unknown';
                }
            }
        }
        return self::$_edition;
    }

    public function getModuleVersionFromDb($module)
    {
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');
        $tableName = $resource->getTableName('core_resource');
        $query = "SELECT * FROM {$tableName} WHERE code='{$module}_setup'";
        $results = $connection->fetchAll($query);
        if (count($results) == 0) {
            return false;
        }
        return $results[0]['version'];
    }
}