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



class Mirasvit_FeedExport_Model_Feed_Generator_Pattern extends Varien_Object
{
    protected $_categories = array();
    protected static $_patterns = array();
    protected $_cache = array();

    public function parsePattern($pattern)
    {
        if (is_array($pattern)) {
            return $pattern;
        }

        if (!isset(self::$_patterns[$pattern])) {
            $matches = array();

            //type A {(....)}
            preg_match('/{\(([^}]*)\)}/', $pattern, $matches);
            if (isset($matches[1])) {
                self::$_patterns[$pattern] = array(
                    'key' => '('.$matches[1].')',
                    'type' => '',
                    'formatters' => array(),
                    'additional' => '',
                );
            } else {
                //type B {...}
                preg_match('/{([^},|]+)([|])?(parent|only_parent|parent_if_empty|grouped|salable_grouped|configurable|bundle)?([^}]*)}/', $pattern, $matches);
                if (!isset($matches[1])) {
                    return false;
                }
                $key = $matches[1];
                $type = trim($matches[3]);

                preg_match_all('/\[([^\]]+)\]/', $matches[4], $matches);
                $formatters = $matches[1];

                $subkey = explode(':', $key);
                $key = $subkey[0];
                $additional = '';
                if (isset($subkey[1])) {
                    $additional = $subkey[1];
                }

                self::$_patterns[$pattern] = array(
                    'key' => $key,
                    'type' => $type,
                    'formatters' => $formatters,
                    'additional' => $additional,
                );
            }
        }

        return self::$_patterns[$pattern];
    }

    public function getPatternValue($content, $scope = null, $obj = null)
    {
        preg_match_all('/{([^}]+)(\sparent|\sonly_parent|\sparent_if_empty|\sgrouped|\ssalable_grouped|\sconfigurable|\sbundle)?([^}]*)}/', $content, $matches);

        foreach ($matches[0] as $pattern) {
            $value = false;
            switch ($scope) {
                case 'product':
                    $model = Mage::getSingleton('feedexport/feed_generator_pattern_product')->setFeed($this->getFeed());
                    $value = $model->getValue($pattern, $obj);
                    break;

                case 'category':
                    $model = Mage::getSingleton('feedexport/feed_generator_pattern_category')->setFeed($this->getFeed());
                    $value = $model->getValue($pattern, $obj);
                    break;

                case 'review':
                    $model = Mage::getSingleton('feedexport/feed_generator_pattern_review')->setFeed($this->getFeed());
                    $value = $model->getValue($pattern, $obj);
                    break;
            }

            if ($value === '' || $value === null || $value === false) {
                $model = Mage::getSingleton('feedexport/feed_generator_pattern_global')->setFeed($this->getFeed());
                $value = $model->getValue($pattern, $obj);
            }

            $value = Mage::helper('feedexport/string')->processString($value, $this->getFeed());

            if (!is_object($value)) {
                $content = str_replace($pattern, $value.'', $content);
            }

            #remove lines with ###TOREMOVE##
            if (strpos($content, '###TOREMOVE###')) {
                $content = explode("\n", $content);
                foreach ($content as $idx => $line) {
                    if (strpos($line, '###TOREMOVE###')) {
                        unset($content[$idx]);
                    }
                }
                $content = implode("\n", $content);
            }
        }

        return $content;
    }

    protected function _getProductAttribute($code)
    {
        if ($this->_attributes == null) {
            $entityTypeId = Mage::getResourceModel('catalog/product')->getEntityType()->getData('entity_type_id');
            $this->_attributes = Mage::getModel('eav/entity_attribute')->getCollection()
                ->setEntityTypeFilter($entityTypeId);
        }

        return $this->_attributes->getItemByColumnValue('attribute_code', $code);
    }

    /**
     * @todo move to better place
     */
    public function getRootCategory()
    {
        return $this->getCategory($this->getStore()->getRootCategoryId());
    }

    public function applyFormatters($pattern, $value)
    {
        if (!is_array($pattern)) {
            return $value;
        }

        $evalHelper = Mage::helper('feedexport/eval');

        foreach ($pattern['formatters'] as $formatter) {
            $value = $evalHelper->execute($value, $formatter);
        }

        return $value;
    }

    public function getStore()
    {
        return Mage::app()->getStore();
    }

    public function getCategory($categoryId)
    {
        if (!isset($this->_categories[$categoryId])) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $path = explode('/', $category->getPath());

            // check that category from this store
            if (in_array($this->getStore()->getRootCategoryId(), $path)) {
                $this->_categories[$categoryId] = $category;
            } else {
                $this->_categories[$categoryId] = false;
            }
        }

        return $this->_categories[$categoryId];
    }
}
