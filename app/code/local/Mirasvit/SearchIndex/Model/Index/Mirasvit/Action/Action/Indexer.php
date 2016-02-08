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



class Mirasvit_SearchIndex_Model_Index_Mirasvit_Action_Action_Indexer extends Mirasvit_SearchIndex_Model_Indexer_Abstract
{
    protected function _getSearchableEntities($storeId, $entityIds, $lastEntityId, $limit = 100)
    {
        $collection = Mage::getModel('action/action')->getCollection();
        $collection->addStoreFilter($storeId)
            ->addFieldToFilter('is_active', 1);

        if ($entityIds) {
            $collection->addFieldToFilter('action_id', array('in' => $entityIds));
        }

        $collection->getSelect()->where('main_table.action_id > ?', $lastEntityId)
            ->limit($limit)
            ->order('main_table.action_id');

        return $collection;
    }
}
