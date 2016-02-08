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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_SearchSphinx_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function testTopLevelCategoryIsAnchor()
    {
        $result = self::SUCCESS;
        $title = 'Search Sphinx: Root Store Category is Anchor';
        $description = array();

        foreach (Mage::app()->getStores() as $store) {
            $rootCategoryId = $store->getRootCategoryId();
            $rootCategory = Mage::getModel('catalog/category')->load($rootCategoryId);
            $categoryName = $rootCategory->getName();
            if ($rootCategory->getIsAnchor() == 0) {
                $result = self::FAILED;
                $description[] = 'Go to the Catalog > Manage Categories';
                $description[] = "Change option 'Is Anchor' to 'Yes' for category '$categoryName (ID: $rootCategoryId)'";
            }
        }

        return array($result, $title, $description);
    }

    public function testProductIndexExists()
    {
        $result = self::SUCCESS;
        $title = 'Search Sphinx: Search indexes are exists';
        $description = '';

        $count = Mage::getModel('searchindex/index')->getCollection()->count();
        if ($count == 0) {
            $result = self::FAILED;
            $description = 'Create required search indexes at Search / Manage Indexes';
        }

        return array($result, $title, $description);
    }

    public function testTablesExists()
    {
        $result = self::SUCCESS;
        $title = 'Search Sphinx: Required tables are exists';
        $description = array();

        $tables = array(
            'catalogsearch/fulltext',
            'searchindex/index',
            'searchsphinx/synonym',
            'searchsphinx/stopword',
        );

        foreach ($tables as $table) {
            if (!$this->dbTableExists($table)) {
                $description[] = "Table '$table' not exists";
                $result = self::FAILED;
            }
        }

        return array($result, $title, $description);
    }

    public function testReindexIsCompleted()
    {
        $result = self::SUCCESS;
        $title = 'Search Sphinx: Search index is valid';
        $description = '';

        if (!$this->dbTableColumnExists('catalogsearch/fulltext', 'searchindex_weight')) {
            $result = self::FAILED;
            $description = 'Please run full search reindex at System / Index Management';
        }

        return array($result, $title, $description);
    }

    public function testExecIsEnabled()
    {
        $result = self::SUCCESS;
        $title = "Search Sphinx: The function 'exec' is enabled";
        $description = '';

        if (!function_exists('exec')) {
            $result = self::FAILED;
            $description = "The function 'exec' is disabled. Please, ask your hosting administrator to enable this function.";
        }

        return array($result, $title, $description);
    }

    public function testDomainNameIsPinged()
    {
        $execIsEnabled = $this->testExecIsEnabled();
        $result = self::SUCCESS;
        $title = 'Search Sphinx: The server can connect to the domain name';
        $description = array();

        if (Mage::getSingleton('searchsphinx/config')->getSearchEngine() === 'sphinx') {
            if ($execIsEnabled[0] == self::SUCCESS) {
                $opts = array('http' => array(
                                'timeout' => 3,
                            ),
                        );
                $context = stream_context_create($opts);

                Mage::register('custom_entry_point', true, true);

                $store = Mage::app()->getStore(0);
                $url = parse_url($store->getUrl());
                $isPinged = file_get_contents($url['scheme'].'://'.$url['host'].'/shell/search.php?ping', false, $context);

                if ($isPinged !== 'ok') {
                    $result = self::FAILED;
                    $description[] = "Your server can't connect to the domain {$url['host']}. In the 'External Sphinx Search' mode extension can't run reindexing via backend.";
                    $description[] = "To solve this issue, you need to ask your hosting administrator to add record '127.0.0.1 {$url['host']}' to the file /etc/hosts.";
                }
            } else {
                $description[] = "Please enable the function 'exec' for this test.";
            }
        } else {
            $description[] = "The test is available only if you use 'External Sphinx Engine'.";
        }

        return array($result, $title, $description);
    }

    public function testProductIndexConfigured()
    {
        $result = self::SUCCESS;
        $title = 'Search Sphinx: Search index "Products" is configured';
        $description = array();

        if ($index = Mage::helper('searchindex/index')->getIndex('mage_catalog_product')) {
            $attributes = Mage::getModel('searchindex/index')->load($index->getId())->getAttributes();

            if (empty($attributes)) {
                $url = Mage::helper('adminhtml')->getUrl('adminhtml/searchindex_index/edit', array('id' => $index->getId()));
                $result = self::WARNING;
                $description[] = "Please configure the search index 'Products' to see more relevant search results.";
                $description[] = "For this, go to the Search / Manage Search Indexes and open index 'Products': <a href='$url' target='_blank'>$url</a>";
                $description[] = "For more information refer to our manual: <a href='http://mirasvit.com/doc/ssu/2.3.2/r/product_index' target='_blank'>http://mirasvit.com/doc/ssu/2.3.2/r/product_index</a>";
            }
        } else {
            $description[] = 'First you need to create a search index for products.';
        }

        return array($result, $title, $description);
    }

    public function testBlockCatalogSearchLayerExists()
    {
        $result = self::SUCCESS;
        $title = 'Search Sphinx: The block "catalogsearch/layer" exists.';
        $description = '';

        $container = $this->getHandleNodesFromLayout('catalogsearch.xml', 'catalogsearch_result_index');

        if (false === array_search('catalogsearch/layer', $container)) {
            $result = self::FAILED;
            $description = 'The block "catalogsearch/layer" does not exist.';
        }

        return array($result, $title, $description);
    }

    public function testCatalogSearchQuerySize()
    {
        $result = self::SUCCESS;
        $title = 'Search Sphinx: catalogsearch_query size';
        $description = array();

        $size = Mage::getModel('catalogsearch/query')->getCollection()->getSize();
        if ($size > 50000) {
            $result = self::FAILED;
            $description[] = "The table `catalogsearch_query` is very big ($size rows). We suggest clear table for improve search performance.";
        }

        return array($result, $title, $description);
    }
}
