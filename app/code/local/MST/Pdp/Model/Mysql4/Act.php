<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Pdp
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Pdp_Model_Mysql4_Act extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        $this->_init('pdp/act', 'act_id');
    }

}