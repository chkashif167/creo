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


class Mirasvit_MstCore_Model_Logger extends Mage_Core_Model_Abstract
{
    const LOG_LEVEL_LOG         = 1;
    const LOG_LEVEL_NOTICE      = 2;
    const LOG_LEVEL_PERFORMANCE = 4;
    const LOG_LEVEL_WARNING     = 8;
    const LOG_LEVEL_EXCEPTION   = 16;
    const LOG_LEVEL_ERROR       = 32;

    protected function _construct()
    {
        $this->_init('mstcore/logger');
    }

    public function save()
    {
        if ($this->isDeleted()) {
            return $this->delete();
        }

        try {
            $this->_beforeSave();
            if ($this->_dataSaveAllowed) {
                $this->_getResource()->save($this);
                $this->_afterSave();
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $this;
    }

    public function clean()
    {
        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $select = $connection->select()->from($resource->getTableName('mstcore/logger'), array('log_id'))
            ->limit(1, 1000)
            ->order('log_id desc');
        $lastId = intval($connection->fetchOne($select));

        if ($lastId) {
            $connection->delete(
                $resource->getTableName('mstcore/logger'),
                array('log_id < '.intval($lastId + 500))
            );
        }

        return $this;
    }
}