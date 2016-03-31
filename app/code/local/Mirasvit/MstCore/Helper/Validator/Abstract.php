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


class Mirasvit_MstCore_Helper_Validator_Abstract extends Mage_Core_Helper_Abstract
{
    const SUCCESS = 1;
    const WARNING = 2;
    const INFO    = 3;
    const FAILED  = 0;

    public function runTests($testType)
    {
        $results = array();

        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (substr($method, 0, strlen($testType)) == $testType) {
                $key = get_class($this).$method;
                try {
                    $results[$key] = call_user_func(array($this, $method));
                } catch (Exception $e) {
                    $results[$key] = array(self::FAILED, "Test '$method'", $e->getMessage());
                }
            }
        }

        return $results;
    }

    public function validateRewrite($class, $classNameB)
    {
        $classNameA = get_class(Mage::getModel($class));
        if ($classNameA == $classNameB) {
            return true;
        } else {
            return "$class must be $classNameB, current rewrite is $classNameA";
        }
    }

    public function dbGetTableEngine($tableName)
    {
        $table = $this->_dbRes()->getTableName($tableName);
        $status = $this->_dbConn()->showTableStatus($table);
        if ($status && isset($status['Engine'])) {
            return $status['Engine'];
        }
    }

    public function dbCheckTables($tables)
    {
        $result = self::SUCCESS;
        $title = 'Required tables exist';
        $description = array();

        foreach ($tables as $table) {
            if (!$this->dbTableExists($table)) {
                $tableName = $this->_dbRes()->getTableName($table);
                $description[] = "Table '$tableName' doesn't exist";
                $result = self::FAILED;
                continue;
            }
            if ($table == 'catalogsearch/fulltext') {
                continue;
            }
            $engine = $this->dbGetTableEngine($table);
            if ($engine != 'InnoDB') {
                $description[] = "Table '$table' has engine $engine. It should have engine InnoDB.";
                $result = self::FAILED;
            }
        }
        return array($result, $title, $description);
    }

    public function dbTableExists($tableName)
    {
        $table = $this->_dbRes()->getTableName($tableName);

        return $this->_dbConn()->showTableStatus($table) !== false;
    }

    public function dbDescribeTable($tableName)
    {
        $table = $this->_dbRes()->getTableName($tableName);

        return $this->_dbConn()->describeTable($table);
    }

    public function dbTableColumnExists($tableName, $column)
    {
        $desribe = $this->dbDescribeTable($tableName);

        return array_key_exists($column, $desribe);
    }

    public function dbTableIsEmpty($table)
    {
        $select = $this->_dbConn()->select()->from($this->_dbRes()->getTableName($table));
        $row = $this->_dbConn()->fetchRow($select);

        if (is_array($row)) {
            return false;
        }

        return true;
    }

    public function ioIsReadable($path)
    {
        if (is_file($path) && !is_readable($path)) {
            return false;
        }

        return true;
    }

    public function ioIsWritable($path)
    {
        if (is_writable($path)) {
            return true;
        }

        return false;
    }

    public function ioNumberOfFiles($path)
    {
        $cnt = 0;
        $dir = new DirectoryIterator($path);
        foreach($dir as $file) {
            $cnt += (is_file($path.DS.$file)) ? 1 : 0;
        }

        return $cnt;
    }

    protected function _dbRes()
    {
        return Mage::getSingleton('core/resource');
    }

    protected function _dbConn()
    {
        return $this->_dbRes()->getConnection('core_write');
    }

    /**
     * @param string $layoutName - e.g. catalogsearch.xml
     * @param string $handleName - e.g. catalogsearch_result_index
     * @return array $container  - one-dimensional array with nodes
     */
    protected function getHandleNodesFromLayout($layoutName, $handleName)
    {
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
            Mage::app()->getDefaultStoreView()->getId(),
            Mage_Core_Model_App_Area::AREA_FRONTEND
        );

        $catalogSearchLayoutFile = Mage::getDesign()->getLayoutFilename($layoutName);
        $catalogSearchXml = new Zend_Config_Xml($catalogSearchLayoutFile, $handleName);
        $catalogSearchArray = $catalogSearchXml->toArray();

        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($catalogSearchArray));
        $container = iterator_to_array($iterator, false);

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $container;
    }
}