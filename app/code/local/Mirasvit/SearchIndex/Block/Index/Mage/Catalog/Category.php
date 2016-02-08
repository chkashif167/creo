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



class Mirasvit_SearchIndex_Block_Index_Mage_Catalog_Category extends Mirasvit_SearchIndex_Block_Index_Template
{
    public function getFullPath($categoryId)
    {
        $rootId = Mage::app()->getStore()->getRootCategoryId();
        $result = array();
        $id = $categoryId;
        do {
            $parent = Mage::getModel('catalog/category')->load($id)->getParentCategory();
            $id = $parent->getId();

            if (!$parent->getId()) {
                break;
            }

            if (!$parent->getIsActive() && $parent->getId() != $rootId) {
                return false;
            }

            if ($parent->getId() != $rootId) {
                $result[] = $parent;
            }
        } while ($parent->getId() != $rootId);

        $result = array_reverse($result);

        return $result;
    }
}
