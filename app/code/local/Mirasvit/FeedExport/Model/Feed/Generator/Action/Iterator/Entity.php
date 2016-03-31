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



class Mirasvit_FeedExport_Model_Feed_Generator_Action_Iterator_Entity
    extends Mirasvit_FeedExport_Model_Feed_Generator_Action_Iterator_Abstract
{
    protected $_format = null;
    protected $_type = null;

    public function init()
    {
        $this->_format = $this->getFeed()->getFormat();
        $this->_type = $this->getType();

        return isset($this->_format['entity'][$this->_type]);
    }

    public function getCollection()
    {
        if ($this->_type == 'product') {
            $collection = Mage::getModel('catalog/product')->getCollection()
                ->joinField('qty', 'cataloginventory/stock_item', 'qty',
                    'product_id=entity_id', '{{table}}.stock_id=1', 'left')
                ->addStoreFilter();

            $this->applyRuleFilter($collection);
        } elseif ($this->_type == 'category') {
            $root = Mage::getModel('catalog/category')->load($this->getFeed()->getStore()->getRootCategoryId());

            $collection = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('name')
                ->addFieldToFilter('entity_id', array('nin' => array(1, 2)));

            if (method_exists($collection, 'addPathFilter')) {
                $collection->addPathFilter($root->getPath());
            }
        } elseif ($this->_type == 'review') {
            $collection = Mage::getModel('review/review')->getResourceCollection();
            $collection->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->addFieldToFilter('entity_id', 1)
                ->setDateOrder()
                ->setPageSize(100)
                ->addRateVotes()
                ->clear();

            $this->applyRuleFilter($collection, 'entity_pk_value');
        }

        return $collection;
    }

    public function callback($row)
    {
        $this->_patternModel = Mage::getSingleton('feedexport/feed_generator_pattern');
        $this->_patternModel->setFeed($this->getFeed());

        if ($this->_type == 'review') {
            $model = Mage::getModel('review/review')->load($row['review_id']);
        } else {
            $model = Mage::getModel('catalog/'.$this->_type)->load($row['entity_id']);
            $model->setStoreId($this->getFeed()->getStoreId());
        }
        $result = $this->_patternModel->getPatternValue($this->_format['entity'][$this->_type], $this->_type, $model);

        return $result;
    }

    public function save($result)
    {
        $content = implode(PHP_EOL, $result).PHP_EOL;

        $filePath = Mage::getSingleton('feedexport/config')->getTmpPath($this->getFeed()->getTmpPathKey()).DS.$this->_type.'.dat';
        Mage::helper('feedexport/io')->write($filePath, $content, 'a');
    }

    private function applyRuleFilter($collection, $onField = 'e.entity_id')
    {
        if (count($this->getFeed()->getRuleIds()) && Mage::app()->getRequest()->getParam('skip') != 'rules') {
            $collection->getSelect()->joinLeft(
                array('rule' => Mage::getSingleton('core/resource')->getTableName('feedexport/feed_product')),
                $onField.'=rule.product_id', array())
                ->where('rule.feed_id = ?', $this->getFeed()->getId())
                ->where('rule.is_new = 1');
        }
    }

    public function start()
    {
    }

    public function finish()
    {
    }
}
