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



class Mirasvit_SearchIndex_Helper_Index extends Mage_Core_Helper_Abstract
{
    protected $_indexes = null;

    public function getIndexes()
    {
        if ($this->_indexes == null) {
            $this->_indexes = array();

            $collection = Mage::getModel('searchindex/index')->getCollection()
                ->addFieldToFilter('is_active', 1);

            foreach ($collection as $index) {
                $model = $index->getIndexInstance();
                if ($model->isAllowedInFrontend() && !isset($this->_indexes[$index->getIndexCode()])) {
                    foreach ($model->getSearchTabs() as $code => $indexModel) {
                        $this->_indexes[$code] = $indexModel;
                    }
                }
            }
        }

        return $this->_indexes;
    }

    public function getIndex($index)
    {
        $indexes = $this->getIndexes();
        if (isset($indexes[$index])) {
            return $indexes[$index];
        }

        return false;
    }

    public function getIndexModel($indexCode)
    {
        if ($indexCode) {
            return Mage::getModel('searchindex/index_'.$indexCode.'_index');
        }

        return false;
    }
}
