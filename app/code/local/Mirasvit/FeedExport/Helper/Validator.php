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


class Mirasvit_FeedExport_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function testTablesExists()
    {
        $result = self::SUCCESS;
        $title = 'Advanced Product Feeds: Required tables are exists';
        $description = array();

        $tables = array(
            'feedexport/feed',
            'feedexport/feed_history',
            'feedexport/template',
            'feedexport/custom_attribute',
            'feedexport/performance_click',
            'feedexport/performance_order',
            'feedexport/performance_aggregated',
            'feedexport/rule',
            'feedexport/rule_product',
            'feedexport/rule_feed',
            'feedexport/feed_product',
            'feedexport/mapping_category',
        );

        foreach ($tables as $table) {
            if (!$this->dbTableExists($table)) {
                $description[] = "Table '$table' not exists";
                $result = self::FAILED;
            }            
        }

        return array($result, $title, $description);
    }

    public function testPermissions()
    {
        $result = self::SUCCESS;
        $title = 'Advanced Product Feeds: Correct permissions on files/folders';
        $description = array();

        $pathes = array(
            Mage::getSingleton('feedexport/config')->getBasePath(),
            Mage::getSingleton('feedexport/config')->getTemplatePath(),
            Mage::getSingleton('feedexport/config')->getRulePath(),
        );

        foreach ($pathes as $path) {
            if (!$this->ioIsReadable($path)) {
                $description[] = "Path '$path' is not readable";
                $result = self::FAILED;
            }

            if (!$this->ioIsWritable($path)) {
                $description[] = "Path '$path' is not writable";
                $result = self::FAILED;
            }            
        }

        return array($result, $title, $description);
    }

    public function testTemplatesAreCopied()
    {
        $result = self::SUCCESS;
        $title = 'Advanced Product Feeds: Templates are copied';
        $description = array();

        $path = Mage::getSingleton('feedexport/config')->getTemplatePath();
        $number = $this->ioNumberOfFiles($path);

        if ($number == 0) {
            $result = self::FAILED;
            $description[] = "The folder '$path' is empty. Templates are not copied.";
            $description[] = "Please copy templates from extension package.";
            $description[] = "Go to Catalog > Manage Fields > Manage Templates.";
            $description[] = "Press the button 'Import Templates', select templates and press 'Import Templates' button.";
        }

        return array($result, $title, $description);
    }

    public function testSftp()
    {
        $result = self::SUCCESS;
        $title = 'Advanced Product Feeds: SFTP';
        $description = array();

        if (!extension_loaded('ssh2')) {
            $result = self::WARNING;
            $description[] = "Please, install php extension 'ssh2', if you plan upload feeds via sftp connection.";
            $description[] = "More information at: <a href='http://www.php.net/manual/en/book.ssh2.php'>http://www.php.net/manual/en/book.ssh2.php</a>";
        }

        return array($result, $title, $description);
    }
}