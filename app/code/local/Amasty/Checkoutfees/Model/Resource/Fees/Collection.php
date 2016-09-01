<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Model_Resource_Fees_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('amcheckoutfees/fees');
    }

    public function validateAllFees()
    {
        foreach ($this as $k => $fee) {
            if (!$fee->validateFee()) {
                $this->removeItemByKey($k);
            }
        }

        return $this;
    }

}