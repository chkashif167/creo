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


class Mirasvit_SearchIndex_Model_Index_External_Vbulletin_Thread_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'External';
    }

    public function getBaseTitle()
    {
        return 'VBulletin Forum';
    }

    public function getPrimaryKey()
    {
        return 'threadid';
    }

    public function getFieldsets()
    {
        return array(
            'External_Database',
            'External_Url'
        );
    }

    public function getAvailableAttributes()
    {
        $result = array(
            'title'   => Mage::helper('searchindex')->__('Title'),
        );

        return $result;
    }

    public function isAllowMultiInstance()
    {
        return true;
    }

    public function getConnection()
    {
        if ($this->getProperty('db_connection_name')) {
            $connName = $this->getProperty('db_connection_name');
            return Mage::getSingleton('core/resource')->getConnection($connName);
        }

        return parent::getConnection();
    }

    public function getCollection()
    {
        Mage::register('searchindex_vbulletin_index', $this, true);
        $collection = new Mirasvit_SearchIndex_Model_Index_External_Vbulletin_Thread_Collection($this);
        $this->joinMatched($collection, 'main_table.threadid');

        return $collection;
    }
}