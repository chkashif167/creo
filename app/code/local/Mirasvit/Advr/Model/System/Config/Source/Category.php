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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Advr_Model_System_Config_Source_Category extends Varien_Object
{
    public function toOptionArray($empty = false)
    {
        $size = Mage::getModel('catalog/category')->getCollection()->getSize();
        if ($size > 100) {
            return false;
        }

        $list = $this->getTreeCategories(Mage::app()->getStore()->getRootCategoryId());

        $result = array();

        if ($empty) {
            $result[''] = '-';
        }

        foreach ($list as $value => $label) {
            $result[$value] = $label;
        }

        return $result;
    }

    public function getTreeCategories($parentId)
    {
        $result = array();

        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('parent_id', array('eq' => $parentId))
            ->addAttributeToSort('position', 'asc');

        foreach ($collection as $category) {
            $result[$category->getId()] = str_repeat('-', $category->getLevel() * 2) . $category->getName();
            $childrens = $category->getChildren();

            if ($childrens) {
                $childrens = $this->getTreeCategories($category->getId(), true);
                foreach ($childrens as $k => $v) {
                    $result[$k] = $v;
                }
            }
        }

        return $result;
    }
}
