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



class Mirasvit_SearchIndex_Model_Index_External_Wordpress_Post_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'External';
    }

    public function getBaseTitle()
    {
        return 'Wordpress Blog';
    }

    public function getPrimaryKey()
    {
        return 'ID';
    }

    public function getFieldsets()
    {
        return array(
            'External_Database',
            'External_Store',
            'External_Url',
        );
    }

    public function getAvailableAttributes()
    {
        $result = array(
            'post_title' => Mage::helper('searchindex')->__('Post Title'),
            'post_content' => Mage::helper('searchindex')->__('Post Content'),
            'post_excerpt' => Mage::helper('searchindex')->__('Post Excerpt'),
        );

        return $result;
    }

    public function isAllowMultiInstance()
    {
        return true;
    }

    public function isAllowedInFrontend()
    {
        $storeIds = $this->getProperty('store_id');
        if (!is_array($storeIds) || count($storeIds) == 0) {
            return true;
        }
        if (in_array(Mage::app()->getStore()->getId(), $storeIds)) {
            return true;
        }
        if (Mage::app()->getStore()->getId() == 0) {
            return true;
        }

        return false;
    }

    public function getConnection()
    {
        if ($this->getProperty('db_connection_name')) {
            $connName = $this->getProperty('db_connection_name');
            $connection = Mage::getSingleton('core/resource')->getConnection($connName);

            return $connection;
        }

        return parent::getConnection();
    }

    public function getCollection()
    {
        Mage::register('searchindex_wordpress_index', $this, true);
        $collection = new Mirasvit_SearchIndex_Model_Index_External_Wordpress_Post_Collection($this);
        $this->joinMatched($collection, 'main_table.ID');

        return $collection;
    }
}
