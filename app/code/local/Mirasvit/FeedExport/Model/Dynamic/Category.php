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


class Mirasvit_FeedExport_Model_Dynamic_Category extends Mage_Core_Model_Abstract
{
    protected $_mapping = null;

    protected function _construct()
    {
        $this->_init('feedexport/dynamic_category');
    }

    public function getMapping()
    {
        if ($this->_mapping == null) {
            $this->_mapping = $this->_buildMapping();
        }

        return $this->_mapping;
    }

    protected function _buildMapping($parentId = 0)
    {
        $mapping = array();
        $userMapping = $this->getData('mapping');

        $collection = Mage::getModel('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('children_count')
            ->addAttributeToFilter('parent_id', $parentId)
            ->setOrder('position', 'asc')
            ->load();

        foreach ($collection as $category) {
            $categoryId = $category->getId();

            if (isset($userMapping[$categoryId])) {
                $map = $userMapping[$categoryId];
            } else {
                $map = '';
            }

            if ($category->getName()) {
                $mapping[$categoryId] = array(
                    'category_id'   => $categoryId,
                    'name'          => $category->getName(),
                    'map'           => $map,
                    'level'         => $category->getLevel(),
                    'path'          => $category->getPath(),
                    'parent_id'     => $category->getParentId(),
                    'has_childs'    => $category->getChildrenCount(),
                );
            }

            if ($category->getChildrenCount()) {
                $childMapping = $this->_buildMapping($category->getId());
                $mapping = $mapping + $childMapping;
            }
        }

        return $mapping;
    }

    public function getMappingValue($categoryId)
    {
        $result = '';

        $mapping = $this->getMapping();

        if (isset($mapping[$categoryId]) && is_array($mapping[$categoryId])) {
            $map = $mapping[$categoryId];
            if ($map['map'] != '') {
                $result = $map['map'];
            } else {
                $path = explode('/', $map['path']);
                $path = array_reverse($path);
                foreach ($path as $id) {
                    if (isset($mapping[$id])) {
                        if ($mapping[$id]['map'] != '') {
                            $result = $mapping[$id]['map'];
                            break;
                        }
                    }
                }
            }
        }

        return $result;
    }
}