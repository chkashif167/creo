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



class Mirasvit_SearchIndex_Model_Index_External_Joomla_Article_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function __construct($resource = null)
    {
        $this->_construct();
        $this->_resource = Mage::getSingleton('core/resource');
        $this->setConnection($this->getIndex()->getConnection());
        $this->setModel('searchindex/index_external_joomla_article_item');
        $this->_initSelect();
    }

    public function getIndex()
    {
        return Mage::helper('searchindex/index')->getIndexModel('external_joomla_article');
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            array('cat' => $this->getIndex()->getProperty('db_table_prefix').'categories'), 'cat.id=main_table.catid',
            array('path'));

       // echo $this->getSelect();die();

        return $this;
    }

    public function getMainTable()
    {
        return $this->getIndex()->getProperty('db_table_prefix').'content';
    }
}
