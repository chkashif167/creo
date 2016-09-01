<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */


class Amasty_Checkoutfees_Model_Resource_Fees extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('amcheckoutfees/fees', 'fees_id');
    }

    public function massDelete($ids)
    {
        $db = $this->_getWriteAdapter();

        $ids[] = 0;
        $cond = $db->quoteInto('fees_id IN(?)', $ids);
        $db->delete($this->getMainTable(), $cond);

        return true;
    }

    public function massEnable($ids)
    {
        $db = $this->_getWriteAdapter();

        $ids[] = 0;
        $cond = $db->quoteInto('fees_id IN(?)', $ids);
        $db->update($this->getMainTable(), array('enabled' => '1'), $cond);

        return true;
    }

    public function massDisable($ids)
    {
        $db = $this->_getWriteAdapter();

        $ids[] = 0;
        $cond = $db->quoteInto('fees_id IN(?)', $ids);
        $db->update($this->getMainTable(), array('enabled' => '0'), $cond);

        return true;
    }
}